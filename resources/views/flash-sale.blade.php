@extends('layouts.app')
@section('title', 'Flash Sale - ' . ($setting->store_name ?? 'Cold Storage'))
@section('content')
    <div style="text-align:center;margin-bottom:2rem;">
        <div
            style="font-size:2.5rem;font-weight:900;background:linear-gradient(135deg,#f59e0b,#ef4444);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">
            ⚡ FLASH SALE</div>
        <p style="color:var(--gray);">Penawaran terbatas, jangan sampai kehabisan!</p>
    </div>

    @if($flashSales->count() > 0)
        <div class="products-grid">
            @foreach($flashSales as $fs)
                <a href="{{ route('product.detail', $fs->product->slug ?? '#') }}" class="product-card">
                    <div class="card-img">
                        @if($fs->product->image)
                            <img src="{{ asset('storage/' . $fs->product->image) }}" alt="{{ $fs->product->name }}" loading="lazy">
                        @else
                            <div
                                style="height:100%;display:flex;align-items:center;justify-content:center;font-size:3rem;background:var(--gray-light);">
                                🛍️</div>
                        @endif
                        <div class="card-badge promo">FLASH</div>
                    </div>
                    <div class="card-body">
                        <div class="card-name">{{ $fs->product->name ?? '' }}</div>
                        @if($fs->variant)
                            <div style="font-size:0.75rem;color:var(--gray);margin-bottom:0.25rem;">{{ $fs->variant->name }}</div>
                        @endif
                        <div class="card-price" style="color:var(--danger);">Rp {{ number_format($fs->flash_price, 0, ',', '.') }}
                        </div>
                        @if($fs->product && $fs->product->base_price > $fs->flash_price)
                            <div style="font-size:0.75rem;text-decoration:line-through;color:var(--gray);">Rp
                                {{ number_format($fs->product->base_price, 0, ',', '.') }}
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="es-icon">⚡</div>
            <h3>Belum ada Flash Sale aktif</h3>
            <p>Pantau terus untuk penawaran terbatas!</p>
            <a href="{{ route('home') }}" class="btn btn-primary" style="margin-top:1rem;">Lihat Katalog</a>
        </div>
    @endif
@endsection
