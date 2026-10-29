<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Batalkan pesanan yang belum dibayar > 1 jam; stok (dan kuota flash sale) otomatis kembali
Artisan::command('orders:cancel-unpaid {--minutes=60}', function () {
    $count = 0;
    // Batas bayar ada di payment_due_at (1 jam; 24 jam kalau menunggu ongkir dari admin, dihitung ulang saat ongkir ditentukan).
    // Pesanan lama tanpa payment_due_at memakai --minutes sejak ordered_at.
    \App\Models\Order::where('status', 'pending')
        ->where(fn($q) => $q->where('payment_due_at', '<=', now())
            ->orWhere(fn($q) => $q->whereNull('payment_due_at')->where('ordered_at', '<=', now()->subMinutes((int) $this->option('minutes')))))
        ->each(function ($order) use (&$count) {
            $order->update(['status' => 'cancelled']); // hook di model Order mengembalikan stok
            $count++;
        });
    $this->info("{$count} pesanan dibatalkan.");
})->purpose('Batalkan pesanan pending yang kedaluwarsa');

\Illuminate\Support\Facades\Schedule::command('orders:cancel-unpaid')->everyFiveMinutes();
