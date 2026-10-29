<?php

namespace Tests\Feature;

use App\Filament\Resources\OrderResource\Pages\ViewOrder;
use App\Filament\Resources\ShippingZoneResource\Pages\CreateShippingZone;
use App\Models\AdminUser;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\ShippingZone;
use App\Models\StoreSetting;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class AdminShippingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        StoreSetting::create(['store_name' => 'Test Store', 'whatsapp_number' => '6281234567890', 'site_mode' => 'live', 'message_template' => 'x']);
        $admin = AdminUser::create(['name' => 'A', 'email' => 'a@a.test', 'password' => bcrypt('x'), 'role' => 'super_admin', 'is_active' => true]);
        $this->actingAs($admin, 'admin');
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    private function pendingOrder(): Order
    {
        $pm = PaymentMethod::create(['name' => 'Transfer', 'type' => 'bank', 'is_active' => true]);
        return Order::create([
            'order_code' => 'TS-ABC12345', 'customer_name' => 'Siti', 'customer_phone' => '081355554321',
            'customer_address' => 'Jl. Sudirman No. 10, Jakarta', 'delivery_method' => 'delivery',
            'shipping_pending' => true, 'payment_method_id' => $pm->id, 'subtotal' => 65000, 'admin_fee' => 0,
            'discount_amount' => 0, 'unique_code' => 780, 'total' => 65780, 'status' => 'pending', 'ordered_at' => now(),
        ]);
    }

    public function test_admin_sets_shipping_fee_on_pending_order_and_invoice_link_appears(): void
    {
        $order = $this->pendingOrder();

        Livewire::test(ViewOrder::class, ['record' => $order->getKey()])
            ->assertActionVisible('set_ongkir')
            ->assertActionHidden('kirim_invoice')                    // belum ada ongkir: belum bisa kirim invoice
            ->callAction('set_ongkir', ['fee' => 60000])
            ->assertHasNoActionErrors();

        $order->refresh();
        $this->assertFalse($order->shipping_pending);
        $this->assertEquals(60000, (float) $order->shipping_fee);
        $this->assertEquals(65000 + 60000 + 780, (float) $order->total);

        Livewire::test(ViewOrder::class, ['record' => $order->getKey()])
            ->assertActionVisible('kirim_invoice');
    }

    public function test_admin_adds_zone_by_choosing_city_from_region_api(): void
    {
        Http::fake([
            '*provinces.json' => Http::response(['data' => [['code' => '33', 'name' => 'Jawa Tengah']]]),
            '*regencies/33.json' => Http::response(['data' => [['code' => '33.01', 'name' => 'Kabupaten Cilacap'], ['code' => '33.02', 'name' => 'Kabupaten Banyumas']]]),
        ]);

        Livewire::test(CreateShippingZone::class)
            ->fillForm(['province_code' => '33', 'regency_code' => '33.02', 'fee' => 40000])
            ->call('create')
            ->assertHasNoFormErrors();

        $zone = ShippingZone::first();
        $this->assertEquals('Kabupaten Banyumas', $zone->name);
        $this->assertEquals('33.02', $zone->regency_code);
        $this->assertEquals(40000, (float) $zone->fee);

        // kota yang sama dua kali ditolak
        Livewire::test(CreateShippingZone::class)
            ->fillForm(['province_code' => '33', 'regency_code' => '33.02', 'fee' => 1])
            ->call('create')
            ->assertHasFormErrors(['regency_code']);
        $this->assertEquals(1, ShippingZone::count());
    }
}
