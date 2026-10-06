<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
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

    public function test_search_products_api(): void
    {
        $category = Category::create(['name' => 'Test', 'slug' => 'test', 'is_active' => true, 'sort_order' => 1]);
        Product::create(['name' => 'Kaos Hitam',  'slug' => 'kaos-hitam',  'category_id' => $category->id, 'base_price' => 50000,  'is_active' => true, 'sort_order' => 1]);
        Product::create(['name' => 'Celana Jeans', 'slug' => 'celana-jeans', 'category_id' => $category->id, 'base_price' => 150000, 'is_active' => true, 'sort_order' => 2]);

        $response = $this->getJson('/api/products/search?q=Kaos');
        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }

    public function test_create_order_api(): void
    {
        $category = Category::create(['name' => 'Test', 'slug' => 'test', 'is_active' => true, 'sort_order' => 1]);
        $product  = Product::create(['name' => 'Test', 'slug' => 'test-p', 'category_id' => $category->id, 'base_price' => 50000, 'stock' => 10, 'is_active' => true, 'sort_order' => 1]);
        $pm       = PaymentMethod::create(['name' => 'Transfer', 'type' => 'bank', 'is_active' => true, 'sort_order' => 1, 'admin_fee' => 0, 'fee_type' => 'fixed']);

        $response = $this->postJson('/api/order', [
            'customer_name'     => 'John',
            'customer_phone'    => '081234567890',
            'payment_method_id' => $pm->id,
            'items'             => [
                ['product_id' => $product->id, 'variant_id' => null, 'quantity' => 2],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('orders', ['customer_name' => 'John']);
    }
}
