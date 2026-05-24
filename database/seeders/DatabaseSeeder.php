<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\Category;
use App\Models\StoreSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        AdminUser::firstOrCreate(
            ['email' => 'admin@coldstorage.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        // Sample Admin biasa
        AdminUser::firstOrCreate(
            ['email' => 'staf@coldstorage.com'],
            [
                'name' => 'Staf Toko',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'permissions' => ['manage_products', 'manage_orders', 'manage_testimonials'],
                'is_active' => true,
            ]
        );

        // Store Settings
        StoreSetting::firstOrCreate(
            ['id' => 1],
            [
                'store_name' => 'Cold Storage',
                'store_description' => 'Toko online terpercaya dengan produk berkualitas',
                'store_email' => 'taufiqstore@gmail.com',
                'whatsapp_number' => '628123456789',
                'message_template' => "Halo Cold Storage! 👋\n\nSaya ingin memesan:\n\n{items}\n\n💰 Subtotal: {subtotal}\n🏦 Biaya Admin: {admin_fee}\n🏷️ Diskon: {discount}\n🔢 Kode Unik: {unique_code}\n💵 *TOTAL: {total}*\n\n💳 Pembayaran: {payment}\n\n👤 Nama: {name}\n📱 HP: {phone}\n📋 Kode Pesanan: {order_code}\n\n📝 Catatan: {note}\n\nMohon konfirmasi pesanan saya. Terima kasih! 🙏",
                'site_mode' => 'live',
            ]
        );

        // Kategori Ikan
        $categories = [
            ['name' => 'Ikan Nila', 'slug' => 'ikan-nila', 'icon' => 'heroicon-o-fish', 'sort_order' => 1],
            ['name' => 'Ikan Lele', 'slug' => 'ikan-lele', 'icon' => 'heroicon-o-fish', 'sort_order' => 2],
            ['name' => 'Ikan Kakap', 'slug' => 'ikan-kakap', 'icon' => 'heroicon-o-fish', 'sort_order' => 3],
            ['name' => 'Ikan Patin', 'slug' => 'ikan-patin', 'icon' => 'heroicon-o-fish', 'sort_order' => 4],
            ['name' => 'Ikan Gurame', 'slug' => 'ikan-gurame', 'icon' => 'heroicon-o-fish', 'sort_order' => 5],
        ];
        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        $this->command->info('✅ Seeder selesai!');
        $this->command->info('📧 Super Admin: admin@coldstorage.com / password');
        $this->command->info('📧 Staf Admin: staf@coldstorage.com / password');
    }
}

