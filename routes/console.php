<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Batalkan pesanan yang belum dibayar > 1 jam; stok (dan kuota flash sale) otomatis kembali
Artisan::command('orders:cancel-unpaid {--minutes=60}', function () {
    $count = 0;
    \App\Models\Order::where('status', 'pending')
        ->where('ordered_at', '<=', now()->subMinutes((int) $this->option('minutes')))
        ->each(function ($order) use (&$count) {
            $order->update(['status' => 'cancelled']); // hook di model Order mengembalikan stok
            $count++;
        });
    $this->info("{$count} pesanan dibatalkan.");
})->purpose('Batalkan pesanan pending yang kedaluwarsa');

\Illuminate\Support\Facades\Schedule::command('orders:cancel-unpaid')->everyFiveMinutes();
