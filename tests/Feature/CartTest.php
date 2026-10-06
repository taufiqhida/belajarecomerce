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
            'customer_name' => 'A', 'customer_phone' => '081234567890', 'payment_method_id' => $pm->id,
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
        \App\Models\Order::first()->update(['ordered_at' => now()->subHours(2)]);

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
            'customer_name' => 'A', 'customer_phone' => '081234567890', 'payment_method_id' => $pm->id,
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
                'customer_name' => 'A', 'customer_phone' => $phone, 'payment_method_id' => $pm->id,
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
            'customer_name' => 'A', 'customer_phone' => '081234567890', 'payment_method_id' => $pm->id, 'discount_code' => 'HEMAT',
            'items' => [['product_id' => $p->id, 'variant_id' => $v->id, 'quantity' => 1]],
        ])->assertOk();
        $this->assertEquals(1, $code->fresh()->used_count);

        \App\Models\Order::first()->update(['status' => 'cancelled']);
        $this->assertEquals(0, $code->fresh()->used_count);
    }
}
