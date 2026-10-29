<?php

namespace Tests\Feature;

use App\Models\StoreSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        StoreSetting::create([
            'store_name'       => 'Test Store',
            'whatsapp_number'  => '6281234567890',
            'site_mode'        => 'live',
            'message_template' => 'Test {items} {total} {payment} {name} {phone} {note} {order_code}',
        ]);
    }

    public function test_cart_page_returns_200(): void
    {
        $response = $this->get('/keranjang');
        $response->assertStatus(200);
    }

    public function test_cart_sync_returns_fresh_data_and_flags_unavailable(): void
    {
        $active = \App\Models\Product::create(['name' => 'Menu Baru', 'slug' => 'menu-baru', 'base_price' => 25000, 'is_active' => true]);
        $off = \App\Models\Product::create(['name' => 'Off', 'slug' => 'off', 'base_price' => 1000, 'is_active' => false]);

        $res = $this->postJson('/api/cart/sync', ['items' => [
            ['key' => 'a', 'id' => $active->id],
            ['key' => 'b', 'id' => $off->id],
            ['key' => 'c', 'id' => 9999],
        ]])->assertOk();

        $res->assertJsonPath('0.available', true)
            ->assertJsonPath('0.name', 'Menu Baru')
            ->assertJsonPath('0.price', 25000)
            ->assertJsonPath('1.available', false)
            ->assertJsonPath('2.available', false);
    }

    private function stockFixture(float $stock): array
    {
        $pm = \App\Models\PaymentMethod::create(['name' => 'Transfer', 'type' => 'bank', 'is_active' => true]);
        $p = \App\Models\Product::create(['name' => 'Layang', 'slug' => 'layang', 'base_price' => 20000, 'stock' => $stock, 'is_active' => true]);
        $v = $p->variants()->create(['name' => '5 kg', 'type' => 'weight', 'weight_kg' => 5, 'price' => 100000]);
        return [$pm, $p, $v];
    }

    private function order($pm, $p, $v, int $qty)
    {
        return $this->postJson('/api/order', [
            'customer_name' => 'A', 'customer_phone' => '081234567890', 'customer_address' => 'Jl. Mawar No. 1, Bitung', 'payment_method_id' => $pm->id,
            'items' => [['product_id' => $p->id, 'variant_id' => $v->id, 'quantity' => $qty]],
        ]);
    }

    public function test_order_reduces_stock_in_kg_and_rejects_when_insufficient(): void
    {
        [$pm, $p, $v] = $this->stockFixture(12);

        $this->order($pm, $p, $v, 2)->assertOk();            // 2 x 5 kg = 10 kg
        $this->assertEquals(2, (float) $p->fresh()->stock);

        $this->order($pm, $p, $v, 1)->assertStatus(422);     // butuh 5 kg, sisa 2
        $this->assertEquals(2, (float) $p->fresh()->stock);
    }

    public function test_cancelling_order_restores_stock(): void
    {
        [$pm, $p, $v] = $this->stockFixture(12);
        $this->order($pm, $p, $v, 2)->assertOk();

        $order = \App\Models\Order::first();
        $order->update(['status' => 'cancelled']);
        $this->assertEquals(12, (float) $p->fresh()->stock);

        $order->update(['status' => 'pending']);
        $this->assertEquals(2, (float) $p->fresh()->stock);
    }

    public function test_flash_sale_quota_is_enforced_and_restored_on_cancel(): void
    {
        [$pm, $p, $v] = $this->stockFixture(100);
        $fs = \App\Models\FlashSale::create([
            'product_id' => $p->id, 'product_variant_id' => $v->id, 'flash_price' => 80000, 'flash_stock' => 5,
            'starts_at' => now()->subHour(), 'ends_at' => now()->addHour(), 'is_active' => true,
        ]);

        $this->order($pm, $p, $v, 1)->assertOk();            // 5 kg, kuota habis
        $this->assertEquals(0, (float) $fs->fresh()->flash_stock);
        $this->assertEquals(95, (float) $p->fresh()->stock);
        $this->assertEquals(80000, (float) \App\Models\OrderItem::first()->price);

        $this->order($pm, $p, $v, 1)->assertStatus(422);     // kuota flash habis
        $this->assertEquals(95, (float) $p->fresh()->stock); // stok biasa tidak ikut berkurang

        \App\Models\Order::first()->update(['status' => 'cancelled']);
        $this->assertEquals(5, (float) $fs->fresh()->flash_stock);
        $this->assertEquals(100, (float) $p->fresh()->stock);
    }

    public function test_unpaid_orders_are_auto_cancelled_and_stock_returns(): void
    {
        [$pm, $p, $v] = $this->stockFixture(12);
        $this->order($pm, $p, $v, 2)->assertOk();
        \App\Models\Order::first()->update(['ordered_at' => now()->subHours(2), 'payment_due_at' => now()->subMinute()]);

        $this->artisan('orders:cancel-unpaid')->assertSuccessful();

        $this->assertEquals('cancelled', \App\Models\Order::first()->status);
        $this->assertEquals(12, (float) $p->fresh()->stock);
    }

    public function test_order_rejects_fake_variant_missing_variant_and_inactive_product(): void
    {
        [$pm, $p, $v] = $this->stockFixture(100);
        $other = \App\Models\Product::create(['name' => 'Lain', 'slug' => 'lain', 'base_price' => 1, 'stock' => 9, 'is_active' => true]);
        $otherV = $other->variants()->create(['name' => '1 kg', 'type' => 'weight', 'weight_kg' => 1, 'price' => 1]);

        $send = fn($vid, $pid = null) => $this->postJson('/api/order', [
            'customer_name' => 'A', 'customer_phone' => '081234567890', 'customer_address' => 'Jl. Mawar No. 1, Bitung', 'payment_method_id' => $pm->id,
            'items' => [['product_id' => $pid ?? $p->id, 'variant_id' => $vid, 'quantity' => 1]],
        ]);

        $send(99999)->assertStatus(422);          // varian palsu
        $send($otherV->id)->assertStatus(422);    // varian milik produk lain
        $send(null)->assertStatus(422);           // produk bervarian tanpa memilih varian

        $p->update(['is_active' => false]);
        $send($v->id)->assertStatus(422);         // produk nonaktif
        $this->assertEquals(100, (float) $p->fresh()->stock);
    }

    public function test_order_rejects_invalid_phone_number(): void
    {
        [$pm, $p, $v] = $this->stockFixture(100);
        foreach (['08', 'abc', '12345678901'] as $phone) {
            $this->postJson('/api/order', [
                'customer_name' => 'A', 'customer_phone' => $phone, 'customer_address' => 'Jl. Mawar No. 1, Bitung', 'payment_method_id' => $pm->id,
                'items' => [['product_id' => $p->id, 'variant_id' => $v->id, 'quantity' => 1]],
            ])->assertStatus(422);
        }
        $this->assertEquals(100, (float) $p->fresh()->stock);
    }

    public function test_cancelling_order_returns_discount_code_usage(): void
    {
        [$pm, $p, $v] = $this->stockFixture(100);
        $code = \App\Models\DiscountCode::create(['code' => 'HEMAT', 'type' => 'fixed', 'value' => 1000, 'min_purchase' => 0, 'max_uses' => 1, 'is_active' => true]);

        $this->postJson('/api/order', [
            'customer_name' => 'A', 'customer_phone' => '081234567890', 'customer_address' => 'Jl. Mawar No. 1, Bitung', 'payment_method_id' => $pm->id, 'discount_code' => 'HEMAT',
            'items' => [['product_id' => $p->id, 'variant_id' => $v->id, 'quantity' => 1]],
        ])->assertOk();
        $this->assertEquals(1, $code->fresh()->used_count);

        \App\Models\Order::first()->update(['status' => 'cancelled']);
        $this->assertEquals(0, $code->fresh()->used_count);
    }

    public function test_pickup_needs_no_address_and_has_no_shipping_fee(): void
    {
        [$pm, $p, $v] = $this->stockFixture(100);
        \App\Models\ShippingZone::create(['name' => 'Bitung Kota', 'fee' => 15000, 'is_active' => true]);

        $this->postJson('/api/order', [
            'customer_name' => 'A', 'customer_phone' => '081234567890', 'payment_method_id' => $pm->id,
            'delivery_method' => 'pickup',
            'items' => [['product_id' => $p->id, 'variant_id' => $v->id, 'quantity' => 1]],
        ])->assertOk();

        $order = \App\Models\Order::first();
        $this->assertEquals('pickup', $order->delivery_method);
        $this->assertNull($order->customer_address);
        $this->assertEquals(0, (float) $order->shipping_fee);
        $this->assertEquals(100000 + $order->unique_code, (float) $order->total);
    }

    public function test_delivery_fee_comes_from_zone_found_on_server_by_region_code(): void
    {
        [$pm, $p, $v] = $this->stockFixture(100);
        \App\Models\ShippingZone::create(['name' => 'Kabupaten Cilacap', 'regency_code' => '33.01', 'fee' => 15000, 'is_active' => true]);
        \App\Models\ShippingZone::create(['name' => 'Kec. Kroya', 'regency_code' => '33.01', 'district_code' => '33.01.06', 'fee' => 25000, 'is_active' => true]);
        \App\Models\ShippingZone::create(['name' => 'Kabupaten Banyumas', 'regency_code' => '33.02', 'fee' => 1, 'is_active' => false]);

        $base = ['customer_name' => 'A', 'customer_phone' => '081234567890', 'payment_method_id' => $pm->id,
            'delivery_method' => 'delivery', 'customer_address' => 'Jl. Mawar No. 1, Cilacap',
            'items' => [['product_id' => $p->id, 'variant_id' => $v->id, 'quantity' => 1]]];

        // tarif kota; "shipping_fee" kiriman klien diabaikan
        $res = $this->postJson('/api/order', $base + ['regency_code' => '33.01', 'district_code' => '33.01.02', 'shipping_fee' => 0])->assertOk();
        $o = \App\Models\Order::latest('id')->first();
        $this->assertEquals(15000, (float) $o->shipping_fee);
        $this->assertFalse($o->shipping_pending);
        $this->assertEquals(100000 + 15000 + $o->unique_code, (float) $o->total);
        $this->assertStringContainsString(rawurlencode('Kabupaten Cilacap'), $res->json('whatsapp_link'));

        // tarif kecamatan lebih diutamakan dari tarif kota
        $this->postJson('/api/order', $base + ['regency_code' => '33.01', 'district_code' => '33.01.06'])->assertOk();
        $this->assertEquals(25000, (float) \App\Models\Order::latest('id')->first()->shipping_fee);
    }

    public function test_region_without_zone_creates_order_waiting_for_admin_shipping_then_admin_sets_fee(): void
    {
        [$pm, $p, $v] = $this->stockFixture(100);
        \App\Models\ShippingZone::create(['name' => 'Kabupaten Banyumas', 'regency_code' => '33.02', 'fee' => 1, 'is_active' => false]); // nonaktif = tidak berlaku

        $base = ['customer_name' => 'Budi', 'customer_phone' => '081234567890', 'payment_method_id' => $pm->id,
            'delivery_method' => 'delivery', 'customer_address' => 'Jl. Sudirman No. 1, Jakarta Selatan',
            'items' => [['product_id' => $p->id, 'variant_id' => $v->id, 'quantity' => 1]]];

        $res = $this->postJson('/api/order', $base + ['regency_code' => '31.74', 'district_code' => '31.74.01'])->assertOk();
        $order = \App\Models\Order::first();
        $this->assertTrue($order->shipping_pending);
        $this->assertEquals(0, (float) $order->shipping_fee);
        $this->assertEquals(100000 + $order->unique_code, (float) $order->total);   // belum termasuk ongkir
        $this->assertStringContainsString(rawurlencode('menunggu konfirmasi admin'), $res->json('whatsapp_link'));
        $this->assertTrue($order->payment_due_at->greaterThan(now()->addHours(23)));   // tidak ikut batal dalam 1 jam

        // tanpa kode wilayah sama sekali (daftar wilayah gagal dimuat): juga menunggu admin
        $this->postJson('/api/order', $base)->assertOk();
        $this->assertTrue(\App\Models\Order::latest('id')->first()->shipping_pending);

        // belum jatuh tempo -> tidak dibatalkan otomatis
        $this->artisan('orders:cancel-unpaid')->assertSuccessful();
        $this->assertEquals('pending', $order->fresh()->status);

        // admin menentukan ongkir: total dihitung ulang, batas bayar 1 jam dari sekarang
        $order->applyShippingFee(60000);
        $order->refresh();
        $this->assertFalse($order->shipping_pending);
        $this->assertEquals(60000, (float) $order->shipping_fee);
        $this->assertEquals(100000 + 60000 + $order->unique_code, (float) $order->total);
        $this->assertTrue($order->payment_due_at->lessThan(now()->addMinutes(61)));

        // link WhatsApp ke pembeli: nomor 62..., total, dan nota bertanda tangan
        $url = urldecode($order->invoiceWhatsappUrl());
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $url);
        $this->assertStringContainsString('Ongkir: Rp 60.000', $url);
        $this->assertStringContainsString('/nota/' . $order->order_code . '?signature=', $url);
    }

    public function test_pickup_order_cannot_get_shipping_fee_and_cancelled_cannot_be_changed(): void
    {
        [$pm, $p, $v] = $this->stockFixture(100);
        $this->postJson('/api/order', ['customer_name' => 'A', 'customer_phone' => '081234567890', 'payment_method_id' => $pm->id,
            'delivery_method' => 'pickup', 'items' => [['product_id' => $p->id, 'variant_id' => $v->id, 'quantity' => 1]]])->assertOk();
        $order = \App\Models\Order::first();
        $this->expectException(\DomainException::class);
        $order->applyShippingFee(5000);
    }

    public function test_zone_name_is_filled_from_region_code_when_blank(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            '*regencies/33.json' => \Illuminate\Support\Facades\Http::response(['data' => [['code' => '33.01', 'name' => 'Kabupaten Cilacap']]]),
        ]);
        $zone = \App\Models\ShippingZone::create(['regency_code' => '33.01', 'fee' => 15000]);
        $this->assertEquals('Kabupaten Cilacap', $zone->name);
        $this->assertEquals('33', $zone->province_code);
    }

    public function test_order_requires_shipping_address_and_saves_it(): void
    {
        [$pm, $p, $v] = $this->stockFixture(100);
        $base = ['customer_name' => 'A', 'customer_phone' => '081234567890', 'payment_method_id' => $pm->id,
            'items' => [['product_id' => $p->id, 'variant_id' => $v->id, 'quantity' => 1]]];

        $this->postJson('/api/order', $base)->assertStatus(422);                                   // tanpa alamat
        $this->postJson('/api/order', $base + ['customer_address' => 'Jl. A'])->assertStatus(422); // terlalu singkat
        $this->assertEquals(100, (float) $p->fresh()->stock);

        $res = $this->postJson('/api/order', $base + ['customer_address' => 'Jl. Mawar No. 1, Bitung'])->assertOk();
        $this->assertEquals('Jl. Mawar No. 1, Bitung', \App\Models\Order::first()->customer_address);
        $this->assertStringContainsString(rawurlencode('Jl. Mawar No. 1, Bitung'), $res->json('whatsapp_link'));
    }

    public function test_wilayah_endpoint_proxies_caches_and_validates(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            '*regencies/33.json' => \Illuminate\Support\Facades\Http::response(['data' => [['code' => '33.01', 'name' => 'Kabupaten Cilacap']]]),
            '*' => \Illuminate\Support\Facades\Http::response('x', 500),
        ]);

        $this->getJson('/api/wilayah/regencies/33')->assertOk()->assertExactJson([['id' => '33.01', 'name' => 'Kabupaten Cilacap']]);
        $this->getJson('/api/wilayah/regencies/33')->assertOk();                  // dari cache
        \Illuminate\Support\Facades\Http::assertSentCount(1);

        $this->getJson('/api/wilayah/regencies')->assertNotFound();               // id wajib
        $this->getJson('/api/wilayah/regencies/33.01')->assertNotFound();         // panjang kode salah
        $this->getJson('/api/wilayah/hack/33')->assertNotFound();                 // level tidak dikenal
        $this->getJson('/api/wilayah/districts/33.01')->assertStatus(503);       // API sumber gagal
    }

    public function test_order_history_needs_code_and_last_4_phone_digits_and_nota_is_signed(): void
    {
        [$pm, $p, $v] = $this->stockFixture(100);
        $this->order($pm, $p, $v, 1)->assertOk();   // HP 081234567890
        $code = \App\Models\Order::first()->order_code;

        $this->get('/riwayat-pesanan')->assertOk()->assertDontSee($code);

        $this->post('/riwayat-pesanan', ['order_code' => $code, 'pin' => '0000'])
            ->assertOk()->assertSee('Pesanan Tidak Ditemukan')->assertDontSee('Jl. Mawar');
        $this->post('/riwayat-pesanan', ['order_code' => 'TS-TIDAKADA', 'pin' => '7890'])
            ->assertOk()->assertSee('Pesanan Tidak Ditemukan');
        $this->post('/riwayat-pesanan', ['order_code' => $code, 'pin' => '78'])
            ->assertSessionHasErrors('pin');

        $this->post('/riwayat-pesanan', ['order_code' => strtolower($code), 'pin' => '7890'])
            ->assertOk()->assertSee($code)->assertSee('Jl. Mawar No. 1, Bitung');

        // Pop up sandi: cek lewat AJAX, salah = 422 (pop up tetap terbuka), benar = ok
        $this->postJson('/riwayat-pesanan', ['order_code' => $code, 'pin' => '0000'])->assertStatus(422);
        $this->postJson('/riwayat-pesanan', ['order_code' => $code, 'pin' => '7890'])->assertOk()->assertJson(['ok' => true]);

        // Nota tanpa tanda tangan ditolak, dengan tautan dari riwayat boleh
        $this->get('/nota/' . $code)->assertForbidden();
        $this->get(\Illuminate\Support\Facades\URL::signedRoute('nota.customer', $code, null, false))->assertOk();
    }
}
