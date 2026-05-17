@extends('layouts.app')

@section('title', ($setting->store_name ?? 'Cold Storage KUD Mino Saroyo') . ' — Ikan Segar Berkualitas')

@section('content')

    {{-- ═══ FLASH SALE ═══ --}}
    @if($flashSales->count() > 0)
        <div class="flash-section">
            <div class="flash-header">
                <div class="flash-label">
                    <span class="flash-icon">⚡</span>
                    Flash Sale
                </div>

                <div class="countdown-wrap" id="countdown">
                    <div class="cd-box">
                        <div class="cd-num" id="cd-h">00</div>
                        <div class="cd-label">Jam</div>
                    </div>
                    <div class="cd-sep">:</div>
                    <div class="cd-box">
                        <div class="cd-num" id="cd-m">00</div>
                        <div class="cd-label">Mnt</div>
                    </div>
                    <div class="cd-sep">:</div>
                    <div class="cd-box">
                        <div class="cd-num" id="cd-s">00</div>
                        <div class="cd-label">Dtk</div>
                    </div>
                </div>

                <a href="{{ route('flash-sale') }}" class="btn btn-outline btn-sm" style="margin-left:auto;">
                    Lihat Semua →
                </a>
            </div>

            <div class="flash-grid">
                @foreach($flashSales as $fs)
                    <a href="{{ route('product.detail', $fs->product->slug ?? '#') }}" class="flash-card">
                        <div class="flash-card-img">
                            @if($fs->product->image)
                                <img src="{{ asset('storage/' . $fs->product->image) }}" alt="{{ $fs->product->name }}">
                            @else
                                <div
                                    style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:rgba(255,255,255,.2);">
                                    🛍️</div>
                            @endif
                            @if($fs->product->base_price > $fs->flash_price)
                                <div class="flash-discount-badge">
                                    -{{ round((($fs->product->base_price - $fs->flash_price) / $fs->product->base_price) * 100) }}%
                                </div>
                            @endif
                        </div>
                        <div class="flash-card-body">
                            <div class="flash-card-name">{{ Str::limit($fs->product->name ?? '', 40) }}</div>
                            <div class="flash-price">Rp {{ number_format($fs->flash_price, 0, ',', '.') }}</div>
                            @if($fs->product->base_price > $fs->flash_price)
                                <div class="flash-original">Rp {{ number_format($fs->product->base_price, 0, ',', '.') }}</div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ═══ CATEGORY FILTER ═══ --}}
    <div class="section-header">
        <h2 class="section-title">
            🐟 Katalog Produk Ikan
            <span class="pill">{{ $products->total() }} item</span>
        </h2>
    </div>

    <div class="filter-bar">
        <a href="{{ route('home') }}" class="filter-chip {{ !request('kategori') ? 'active' : '' }}">
            Semua
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('home', ['kategori' => $cat->slug]) }}"
                class="filter-chip {{ request('kategori') === $cat->slug ? 'active' : '' }}">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>

    {{-- ═══ PRODUCTS GRID ═══ --}}
    @if($products->count() > 0)
        <div class="products-grid">
            @foreach($products as $product)
                @php $activeFs = $product->flashSales->first(); @endphp
                <a href="{{ route('product.detail', $product->slug) }}" class="product-card">
                    <div class="card-img">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy">
                        @else
                            <div
                                style="height:100%;display:flex;align-items:center;justify-content:center;font-size:3rem;color:var(--muted);opacity:.3;">
                                🛍️</div>
                        @endif

                        @if($activeFs)
                            <div class="card-badge flash">⚡ Flash</div>
                        @elseif($product->badge)
                            <div class="card-badge {{ $product->badge }}">{{ $product->badge_label }}</div>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="card-category">{{ $product->category->name ?? '' }}</div>
                        <div class="card-name">{{ $product->name }}</div>
                        <div class="card-price">
                            @if($activeFs)
                                <span style="color:var(--red);font-size:1rem;font-weight:800;">
                                    Rp {{ number_format($activeFs->flash_price, 0, ',', '.') }}
                                </span>
                                <span style="text-decoration:line-through;color:var(--muted);font-size:0.75rem;margin-left:4px;">
                                    Rp {{ number_format($product->base_price, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="from-text">mulai </span>
                                Rp {{ number_format($product->base_price, 0, ',', '.') }}
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>


        {{-- PAGINATION --}}
        <div class="pagination">
            {{ $products->withQueryString()->links('pagination.default') }}
        </div>

    @else
        <div class="empty-state">
            <i class="fas fa-fish empty-state-icon"></i>
            <h3>Produk tidak ditemukan</h3>
            <p>Coba kata kunci atau kategori lain</p>
            <a href="{{ route('home') }}" class="btn btn-primary" style="margin-top:1.5rem;">
                Lihat Semua Produk Ikan
            </a>
        </div>
    @endif

    {{-- ═══ CUSTOMER TERBAIK ═══ --}}
    @if(isset($topCustomers) && $topCustomers->count() > 0)
        <div style="margin-top:3rem;padding-top:2rem;border-top:1.5px solid var(--border);">
            <div class="section-header">
                <h2 class="section-title">
                    🏆 Customer Terbaik
                    <span class="pill">Top {{ $topCustomers->count() }}</span>
                </h2>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem;">
                @foreach($topCustomers as $i => $cust)
                    @php
                        $name = $cust->customer_name;
                        $maskedName = strlen($name) <= 2 ? $name[0] . '*' : $name[0] . '*****' . substr($name, -1);
                        $phone = $cust->customer_phone;
                        $maskedPhone = substr($phone, 0, 4) . '**';
                        $medal = match($i) { 0 => '🥇', 1 => '🥈', 2 => '🥉', default => '#' . ($i + 1) };
                    @endphp
                    <div style="background:var(--white);border:1px solid var(--border-2);border-radius:14px;padding:1rem 1.15rem;display:flex;align-items:center;gap:.85rem;box-shadow:var(--shadow-xs);transition:all .2s;"
                        onmouseenter="this.style.boxShadow='var(--shadow-sm)';this.style.transform='translateY(-2px)'"
                        onmouseleave="this.style.boxShadow='var(--shadow-xs)';this.style.transform='none'">
                        <div style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,{{ $i < 3 ? '#fbbf24,#f59e0b' : 'var(--primary),var(--accent)' }});display:flex;align-items:center;justify-content:center;font-size:{{ $i < 3 ? '1.2rem' : '0.8rem' }};color:white;font-weight:800;flex-shrink:0;">
                            {{ $medal }}
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-weight:700;font-size:0.88rem;">{{ $maskedName }}</div>
                            <div style="font-size:0.72rem;color:var(--muted);">📱 {{ $maskedPhone }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:0.82rem;font-weight:800;color:var(--primary-d);">{{ $cust->total_orders }}x</div>
                            <div style="font-size:0.65rem;color:var(--muted);">pesanan</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

@endsection

@section('scripts')
    <script>
        // Flash Sale Countdown
        @if($flashSales->count() > 0)
            const fsEndTime = new Date('{{ $flashSales->first()->ends_at->toISOString() }}');
            function tickCountdown() {
                const diff = fsEndTime - new Date();
                if (diff <= 0) {
                    document.getElementById('countdown').innerHTML = '<span style="color:rgba(255,255,255,.5);font-size:.8rem;">Flash sale berakhir</span>';
                    return;
                }
                const h = Math.floor(diff / 3600000);
                const m = Math.floor((diff % 3600000) / 60000);
                const s = Math.floor((diff % 60000) / 1000);
                document.getElementById('cd-h').textContent = String(h).padStart(2, '0');
                document.getElementById('cd-m').textContent = String(m).padStart(2, '0');
                document.getElementById('cd-s').textContent = String(s).padStart(2, '0');
            }
            tickCountdown();
            setInterval(tickCountdown, 1000);
        @endif
    </script>
@endsection