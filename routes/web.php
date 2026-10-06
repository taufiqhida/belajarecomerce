<?php

use App\Http\Controllers\StoreFrontController;
use App\Http\Middleware\CheckSiteMode;
use App\Models\Article;
use Illuminate\Support\Facades\Route;

// Public store routes
Route::middleware([CheckSiteMode::class])->group(function () {
    Route::get('/', [StoreFrontController::class, 'home'])->name('home');
    Route::get('/produk/{slug}', [StoreFrontController::class, 'productDetail'])->name('product.detail');
    Route::get('/flash-sale', [StoreFrontController::class, 'flashSale'])->name('flash-sale');
    Route::get('/keranjang', [StoreFrontController::class, 'cart'])->name('cart');
    Route::get('/artikel', [StoreFrontController::class, 'articles'])->name('articles');
    Route::get('/artikel/{slug}', [StoreFrontController::class, 'articleDetail'])->name('article.detail');
    Route::get('/riwayat-pesanan', [StoreFrontController::class, 'invoice'])->name('riwayat-pesanan');
    Route::get('/nota/{order_code}', [StoreFrontController::class, 'printCustomerNota'])->name('nota.customer');
});

// Halaman status — hanya bisa diakses saat mode aktif
Route::get('/maintenance', function () {
    $setting = \App\Models\StoreSetting::current();
    if ($setting->site_mode !== 'maintenance') {
        return redirect()->route('home');
    }
    return view('maintenance');
})->name('maintenance');

Route::get('/coming-soon', function () {
    $setting = \App\Models\StoreSetting::current();
    if ($setting->site_mode !== 'coming_soon') {
        return redirect()->route('home');
    }
    return view('coming-soon', compact('setting'));
})->name('coming-soon');

// API
Route::prefix('api')->group(function () {
    Route::get('/products/search', [StoreFrontController::class, 'searchProducts']);
    Route::post('/cart/sync', [StoreFrontController::class, 'syncCart']);
    Route::get('/settings', [StoreFrontController::class, 'getSettings']);
    Route::post('/discount/validate', [StoreFrontController::class, 'validateDiscount']);
    Route::post('/order', [StoreFrontController::class, 'createOrder']);
    Route::post('/testimonial/validate-order', [StoreFrontController::class, 'validateOrderCode']);
    Route::post('/testimonial/submit', [StoreFrontController::class, 'submitTestimonial']);
});

// Nota cetak admin
Route::get('/admin-nota/{order}', [StoreFrontController::class, 'printNota'])
    ->name('admin.nota')
    ->middleware('auth:admin');

// Cetak semua pesanan (by filter)
Route::get('/admin-print-orders', [StoreFrontController::class, 'printOrders'])
    ->name('admin.print-orders')
    ->middleware('auth:admin');

