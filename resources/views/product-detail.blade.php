@extends('layouts.app')

@section('title', $product->name . ' - ' . ($setting->store_name ?? 'Cold Storage'))

@section('head')
    <style>
        .product-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: start;
        }

        @media(max-width:768px) {
            .product-detail-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
        }

        .product-gallery {
            position: sticky;
            top: 80px;
        }

        .gallery-main {
            aspect-ratio: 1;
            background: #f8fafc;
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 0.75rem;
        }

        .gallery-main img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gallery-thumbs {
            display: flex;
            gap: 0.5rem;
        }

        .gallery-thumb {
            width: 64px;
            height: 64px;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
        }

        .gallery-thumb.active {
            border-color: var(--primary);
        }

        .gallery-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-info .pi-category {
            font-size: 0.75rem;
            color: var(--primary-d);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .product-info .pi-name {
            font-size: 1.75rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 0.75rem;
        }

        .product-info .pi-price {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-d);
            margin-bottom: 1.25rem;
        }

        .product-info .pi-desc {
            color: var(--mid);
            font-size: 0.9rem;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }

        .variant-label {
            font-size: 0.825rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .variant-required-label {
            color: #ef4444;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .variants-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .variant-warning {
            font-size: 0.75rem;
            color: #ef4444;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            animation: pulse-warn 1.5s ease-in-out infinite;
        }

        @keyframes pulse-warn {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .variant-warning.hidden {
            display: none;
        }

        .variant-option {
            padding: 0.45rem 1rem;
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            font-size: 0.825rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
        }

        .variant-option.selected {
            border-color: var(--primary);
            background: var(--primary);
            color: white;
        }

        .variant-option:hover:not(.selected) {
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn:disabled,
        .btn[disabled] {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .qty-control {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .qty-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            background: white;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            font-weight: 700;
        }

        .qty-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .qty-num {
            font-size: 1.1rem;
            font-weight: 700;
            min-width: 30px;
            text-align: center;
        }

        .sale-badge-strip {
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            margin-bottom: 1.25rem;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sale-badge-strip .sb-price {
            font-size: 1.3rem;
            font-weight: 800;
        }

        /* ═══ TESTIMONIAL SECTION ═══ */
        .testimonial-section {
            margin-top: 3rem;
            padding-top: 2.5rem;
            border-top: 1.5px solid var(--border);
        }

        .testi-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .testi-title {
            font-size: 1.25rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .testi-count {
            background: var(--primary-l);
            color: var(--primary-d);
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.15rem 0.6rem;
            border-radius: 999px;
        }

        .testi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .testi-card {
            background: var(--white);
            border: 1px solid var(--border-2);
            border-radius: 16px;
            padding: 1.25rem;
            box-shadow: var(--shadow-xs);
            transition: all 0.2s;
        }

        .testi-card:hover {
            box-shadow: var(--shadow-sm);
            transform: translateY(-2px);
        }

        .testi-card-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .testi-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .testi-meta {
            flex: 1;
            min-width: 0;
        }

        .testi-name {
            font-weight: 700;
            font-size: 0.875rem;
            line-height: 1.2;
        }

        .testi-product {
            font-size: 0.72rem;
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .testi-stars {
            display: flex;
            gap: 2px;
            margin-bottom: 0.5rem;
        }

        .testi-stars .star {
            color: #fbbf24;
            font-size: 0.8rem;
        }

        .testi-stars .star.empty {
            color: #e2e8f0;
        }

        .testi-content {
            font-size: 0.84rem;
            color: var(--mid);
            line-height: 1.6;
        }

        .testi-date {
            font-size: 0.68rem;
            color: var(--muted);
            margin-top: 0.6rem;
        }

        .testi-empty {
            text-align: center;
            padding: 2rem;
            color: var(--muted);
            font-size: 0.9rem;
        }

        /* Form */
        .testi-form-card {
            background: linear-gradient(135deg, var(--primary-xl), #fff);
            border: 1.5px solid var(--primary-l);
            border-radius: 16px;
            padding: 1.5rem;
        }

        .testi-form-title {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .testi-step {
            display: none;
        }

        .testi-step.active {
            display: block;
        }

        .star-input {
            display: flex;
            gap: 4px;
            margin-bottom: 1rem;
        }

        .star-input .star-btn {
            font-size: 1.75rem;
            color: #e2e8f0;
            cursor: pointer;
            transition: all 0.15s;
            background: none;
            border: none;
            padding: 0;
            line-height: 1;
        }

        .star-input .star-btn.active {
            color: #fbbf24;
            transform: scale(1.1);
        }

        .star-input .star-btn:hover {
            color: #fbbf24;
            transform: scale(1.2);
        }

        .testi-verified {
            background: var(--primary-l);
            color: var(--primary-d);
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.25rem 0.65rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            margin-bottom: 0.75rem;
        }
    </style>
@endsection

@section('content')
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('home') }}"
            style="color:var(--mid);font-size:0.85rem;font-weight:600;display:inline-flex;align-items:center;gap:.35rem;"><i
                class="fas fa-arrow-left" style="font-size:.75rem;"></i> Kembali ke Katalog</a>
    </div>

    <div class="product-detail-grid">
        {{-- GALLERY --}}
        <div class="product-gallery">
            <div class="gallery-main">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" id="mainImage">
                @else
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:4rem;color:var(--muted);opacity:.3;"
                        id="mainImage">🛍️</div>
                @endif
            </div>
            @if(is_array($product->images) && count($product->images) > 0)
                <div class="gallery-thumbs">
                    @if($product->image)
                        <div class="gallery-thumb active" onclick="changeImg('{{ asset('storage/' . $product->image) }}', this)">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="thumb">
                        </div>
                    @endif
                    @foreach($product->images as $img)
                        <div class="gallery-thumb" onclick="changeImg('{{ asset('storage/' . $img) }}', this)">
                            <img src="{{ asset('storage/' . $img) }}" alt="thumb">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- INFO --}}
        <div class="product-info">
            <div class="pi-category">{{ $product->category->name ?? '' }}</div>
            <div class="pi-name">{{ $product->name }}</div>

            @if($flashSale)
                <div class="sale-badge-strip">
                    <span>⚡ Flash Sale!</span>
                    <span class="sb-price">Rp {{ number_format($flashSale->flash_price, 0, ',', '.') }}</span>
                    <span style="text-decoration:line-through;opacity:0.7;font-size:0.85rem;">Rp
                        {{ number_format($product->base_price, 0, ',', '.') }}</span>
                </div>
            @else
                <div class="pi-price" id="mainPriceDisplay">Rp {{ number_format($product->base_price, 0, ',', '.') }}</div>
            @endif

            @if($product->description)
                <div class="pi-desc">{!! $product->description !!}</div>
            @endif

            @if($product->variants->count() > 0)
                <div class="variant-label">Pilih Varian: <span class="variant-required-label">* Wajib dipilih</span></div>
                <div class="variants-grid" id="variantGroup">
                    @foreach($product->variants as $variant)
                        @php
                            $variantFlash = $product->flashSales->first(fn($fs) => $fs->product_variant_id == $variant->id);
                        @endphp
                        <div class="variant-option"
                            data-id="{{ $variant->id }}"
                            data-price="{{ $variant->price }}"
                            data-name="{{ $variant->name }}"
                            data-flash-price="{{ $variantFlash ? $variantFlash->flash_price : '' }}"
                            onclick="selectVariant(this)">
                            {{ $variant->name }}
                            <br>
                            @if($variantFlash)
                                <small style="color:#ef4444;font-weight:700;">Rp {{ number_format($variantFlash->flash_price, 0, ',', '.') }}</small>
                                <small style="text-decoration:line-through;color:#94a3b8;"> Rp {{ number_format($variant->price, 0, ',', '.') }}</small>
                            @else
                                <small>Rp {{ number_format($variant->price, 0, ',', '.') }}</small>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="variant-warning" id="variantWarning">
                    <i class="fas fa-exclamation-circle"></i> Silakan pilih varian terlebih dahulu
                </div>
            @endif

            <div class="qty-control">
                <span style="font-size:0.825rem;font-weight:600;">Jumlah:</span>
                <button class="qty-btn" onclick="adjustQty(-1)">−</button>
                <span class="qty-num" id="qtyDisplay">1</span>
                <button class="qty-btn" onclick="adjustQty(1)">+</button>
            </div>

            <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
                <button onclick="handleAddToCart()" class="btn btn-ghost" id="btnAddCart" style="flex:1;min-width:160px;"
                    @if($product->variants->count() > 0) disabled @endif>
                    <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                </button>
                <button onclick="openCheckout()" class="btn btn-wa" id="btnCheckout" style="flex:1;min-width:200px;"
                    @if($product->variants->count() > 0) disabled @endif>
                    <i class="fab fa-whatsapp"></i> Pesan via WhatsApp
                </button>
            </div>

            @if($soldCount > 0)
                <p style="margin-top:0.75rem;font-size:0.8rem;color:var(--muted);">
                    <i class="fas fa-fire" style="font-size:.7rem;color:#ef4444;"></i>
                    Produk terjual sebanyak: <strong style="color:var(--dark);">{{ $soldCount }} pcs</strong>
                </p>
            @endif
        </div>
    </div>

    {{-- CHECKOUT MODAL --}}
    <div class="overlay" id="checkoutModal">
        <div class="modal" style="max-width:560px;">
            <div class="modal-header">
                <div class="modal-title">🛒 Checkout Pesanan</div>
                <button class="modal-close" onclick="closeModal('checkoutModal')">✕</button>
            </div>

            {{-- Order Summary --}}
            <div
                style="background:var(--light);border-radius:12px;padding:1rem;margin-bottom:1.25rem;border:1px solid var(--border-2);">
                <div style="font-weight:700;margin-bottom:0.5rem;">{{ $product->name }}</div>
                <div id="summaryVariant" style="font-size:0.825rem;color:var(--muted);"></div>
                <div style="margin-top:0.35rem;">Qty: <strong id="summaryQty">1</strong> × <strong id="summaryPrice">Rp
                        {{ number_format($product->base_price, 0, ',', '.') }}</strong></div>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" class="form-control" id="customerName" placeholder="John Doe">
            </div>
            <div class="form-group">
                <label class="form-label">Nomor HP / WhatsApp *</label>
                <input type="tel" class="form-control" id="customerPhone" placeholder="08123456789">
            </div>
            <div class="form-group">
                <label class="form-label">Metode Pembayaran *</label>
                <select class="form-control" id="paymentMethod">
                    <option value="">-- Pilih Metode --</option>
                    @foreach($paymentMethods as $pm)
                        <option value="{{ $pm->id }}" data-fee="{{ $pm->admin_fee }}" data-fee-type="{{ $pm->fee_type }}">
                            {{ $pm->name }} ({{ ucfirst($pm->type) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Kode Diskon (opsional)</label>
                <div class="discount-row">
                    <input type="text" class="form-control" id="discountCode" placeholder="PROMO10"
                        style="text-transform:uppercase;">
                    <button class="btn btn-ghost" onclick="validateDiscount()" style="white-space:nowrap;">Cek</button>
                </div>
                <div id="discountMsg" style="font-size:0.75rem;margin-top:0.35rem;"></div>
            </div>
            <div class="form-group">
                <label class="form-label">Catatan (opsional)</label>
                <textarea class="form-control" id="customerNote"
                    placeholder="Warna, ukuran, atau permintaan khusus..."></textarea>
            </div>

            {{-- Price Summary --}}
            <div
                style="background:var(--light);border-radius:12px;padding:1rem;margin-bottom:1.25rem;font-size:0.875rem;border:1px solid var(--border-2);">
                <div style="display:flex;justify-content:space-between;margin-bottom:0.3rem;"><span
                        style="color:var(--mid)">Subtotal</span><span id="calcSubtotal">Rp 0</span></div>
                <div style="display:flex;justify-content:space-between;margin-bottom:0.3rem;"><span
                        style="color:var(--mid)">Biaya Admin</span><span id="calcFee">Rp 0</span></div>
                <div style="display:flex;justify-content:space-between;margin-bottom:0.3rem;display:none;" id="discountRow">
                    <span style="color:var(--mid)">Diskon</span><span id="calcDiscount" style="color:var(--primary);">-Rp
                        0</span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:0.3rem;"><span
                        style="color:var(--mid)">Kode Unik</span><span id="calcUnique">xxx</span></div>
                <div
                    style="display:flex;justify-content:space-between;font-weight:800;font-size:1rem;padding-top:0.6rem;border-top:1.5px solid var(--border);margin-top:0.4rem;">
                    <span>TOTAL</span><span id="calcTotal" style="color:var(--primary-d);">Rp 0</span>
                </div>
            </div>

            <button class="btn btn-wa btn-block" style="font-size:1rem;" id="orderBtn" onclick="submitOrder()">
                <i class="fab fa-whatsapp"></i> Kirim Pesanan via WhatsApp
            </button>
        </div>
    </div>

    {{-- TESTIMONIAL SECTION --}}
    <div class="testimonial-section">
        <div class="testi-header">
            <div class="testi-title">
                ⭐ Ulasan Pembeli
                <span class="testi-count">{{ $testimonials->count() }} ulasan</span>
            </div>
        </div>

        {{-- Existing Reviews --}}
        @if($testimonials->count() > 0)
            <div class="testi-grid">
                @foreach($testimonials as $testi)
                    <div class="testi-card">
                        <div class="testi-card-header">
                            <div class="testi-avatar">{{ strtoupper(substr($testi->customer_name, 0, 1)) }}</div>
                            <div class="testi-meta">
                                <div class="testi-name">{{ $testi->customer_name }}</div>
                                <div class="testi-product">🛍️ {{ $testi->product_name }}</div>
                            </div>
                        </div>
                        <div class="testi-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star star {{ $i <= $testi->rating ? '' : 'empty' }}"></i>
                            @endfor
                        </div>
                        <div class="testi-content">{{ $testi->content }}</div>
                        <div class="testi-date">{{ $testi->created_at->diffForHumans() }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="testi-empty">
                <div style="font-size:2.5rem;margin-bottom:0.5rem;">💬</div>
                Belum ada ulasan untuk produk ini. Jadilah yang pertama!
            </div>
        @endif

        {{-- Testimonial Form --}}
        <div class="testi-form-card">
            <div class="testi-form-title">✍️ Tulis Ulasan Anda</div>

            {{-- Step 1: Enter Order Code --}}
            <div class="testi-step active" id="testiStep1">
                <div class="form-group">
                    <label class="form-label">Kode Pembelian *</label>
                    <div style="display:flex;gap:0.5rem;">
                        <input type="text" class="form-control" id="testiOrderCode"
                            placeholder="Masukkan kode pembelian (contoh: TS-XXXXXXXX)" style="text-transform:uppercase;">
                        <button class="btn btn-primary" onclick="validateOrderForTestimonial()" id="testiValidateBtn"
                            style="white-space:nowrap;">
                            Verifikasi
                        </button>
                    </div>
                    <div id="testiCodeMsg" style="font-size:0.75rem;margin-top:0.35rem;"></div>
                </div>
            </div>

            {{-- Step 2: Rating + Message --}}
            <div class="testi-step" id="testiStep2">
                <div class="testi-verified">
                    <i class="fas fa-check-circle"></i>
                    <span id="testiVerifiedName">-</span> — <span id="testiVerifiedProduct">-</span>
                </div>

                <div class="form-group">
                    <label class="form-label">Rating *</label>
                    <div class="star-input" id="starInput">
                        <button class="star-btn" data-val="1" onclick="setRating(1)">★</button>
                        <button class="star-btn" data-val="2" onclick="setRating(2)">★</button>
                        <button class="star-btn" data-val="3" onclick="setRating(3)">★</button>
                        <button class="star-btn" data-val="4" onclick="setRating(4)">★</button>
                        <button class="star-btn" data-val="5" onclick="setRating(5)">★</button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Pesan / Ulasan *</label>
                    <textarea class="form-control" id="testiMessage" placeholder="Ceritakan pengalaman Anda..." rows="3"
                        maxlength="500"></textarea>
                    <div style="font-size:0.7rem;color:var(--muted);margin-top:0.25rem;text-align:right;">
                        <span id="testiCharCount">0</span>/500 karakter
                    </div>
                </div>

                <button class="btn btn-primary btn-block" onclick="submitTestimonial()" id="testiSubmitBtn">
                    <i class="fas fa-paper-plane"></i> Kirim Ulasan
                </button>
            </div>

            {{-- Step 3: Success --}}
            <div class="testi-step" id="testiStep3">
                <div style="text-align:center;padding:1.5rem 0;">
                    <div style="font-size:3rem;margin-bottom:0.75rem;">🎉</div>
                    <div style="font-weight:700;font-size:1.05rem;margin-bottom:0.35rem;">Terima Kasih!</div>
                    <div style="color:var(--mid);font-size:0.85rem;">Ulasan Anda akan ditampilkan setelah disetujui admin.
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        let selectedVariantId = null;
        let selectedVariantName = '';
        let selectedVariantPrice = {{ $flashSale ? $flashSale->flash_price : $product->base_price }};
        let quantity = 1;
        let discountData = null;
        let uniqueCode = 0;

        // Generate kode unik acak (1-999)
        function generateUniqueCode() {
            uniqueCode = Math.floor(Math.random() * 999) + 1;
            return uniqueCode;
        }

        // Gallery
        function changeImg(url, thumb) {
            document.getElementById('mainImage').src = url;
            document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
        }

        // Variant
        const hasVariants = {{ $product->variants->count() > 0 ? 'true' : 'false' }};

        function selectVariant(el) {
            document.querySelectorAll('.variant-option').forEach(v => v.classList.remove('selected'));
            el.classList.add('selected');
            selectedVariantId = el.dataset.id;
            selectedVariantName = el.dataset.name;
            selectedVariantPrice = parseFloat(el.dataset.price);

            // Cek apakah varian ini punya flash price
            const flashPrice = el.dataset.flashPrice ? parseFloat(el.dataset.flashPrice) : null;
            const effectivePrice = flashPrice !== null ? flashPrice : selectedVariantPrice;

            // Simpan effective price untuk digunakan di cart & checkout
            selectedVariantPrice = effectivePrice;

            // Update display harga
            const mainPrice = document.getElementById('mainPriceDisplay');
            if (mainPrice) {
                if (flashPrice !== null) {
                    mainPrice.innerHTML = '<span style="color:#ef4444;font-weight:800;">Rp ' + Number(flashPrice).toLocaleString('id') + '</span> '
                        + '<span style="text-decoration:line-through;color:#94a3b8;font-size:0.85rem;">Rp ' + Number(parseFloat(el.dataset.price)).toLocaleString('id') + '</span>';
                } else {
                    mainPrice.textContent = 'Rp ' + Number(effectivePrice).toLocaleString('id');
                }
            }

            document.getElementById('summaryVariant').textContent = 'Varian: ' + el.dataset.name;
            document.getElementById('summaryPrice').textContent = 'Rp ' + Number(effectivePrice).toLocaleString('id');

            // Sembunyikan warning & aktifkan tombol
            const warning = document.getElementById('variantWarning');
            if (warning) warning.classList.add('hidden');
            const btnCart = document.getElementById('btnAddCart');
            const btnCheckout = document.getElementById('btnCheckout');
            if (btnCart) btnCart.disabled = false;
            if (btnCheckout) btnCheckout.disabled = false;

            updateCalc();
        }

        // Qty
        function adjustQty(d) {
            quantity = Math.max(1, quantity + d);
            document.getElementById('qtyDisplay').textContent = quantity;
            document.getElementById('summaryQty').textContent = quantity;
            updateCalc();
        }
        // Tambah ke Keranjang
        function handleAddToCart() {
            if (hasVariants && !selectedVariantId) {
                toast('Silakan pilih varian terlebih dahulu', 'err');
                const warning = document.getElementById('variantWarning');
                if (warning) warning.classList.remove('hidden');
                return;
            }

            const item = {
                id: {{ $product->id }},
                name: '{{ addslashes($product->name) }}',
                price: selectedVariantPrice,   // sudah = flash price jika ada
                image: '{{ $product->image ? asset("storage/" . $product->image) : "" }}',
                variant: selectedVariantName || null,
                variantId: selectedVariantId || null,
            };

            for (let i = 0; i < quantity; i++) {
                addToCart(item);
            }

            toast('Ditambahkan ke keranjang! (' + quantity + 'x)');
        }

        // Checkout — generate kode unik saat modal dibuka
        function openCheckout() {
            if (hasVariants && !selectedVariantId) {
                toast('Silakan pilih varian terlebih dahulu', 'err');
                const warning = document.getElementById('variantWarning');
                if (warning) warning.classList.remove('hidden');
                return;
            }

            generateUniqueCode();
            document.getElementById('summaryQty').textContent = quantity;
            document.getElementById('summaryPrice').textContent = 'Rp ' + Number(selectedVariantPrice).toLocaleString('id');
            updateCalc();
            document.getElementById('checkoutModal').classList.add('open');
        }

        function closeModal(id) { document.getElementById(id).classList.remove('open'); }

        document.getElementById('paymentMethod').addEventListener('change', updateCalc);

        const fmt = n => 'Rp ' + Math.round(n).toLocaleString('id');

        function updateCalc() {
            const subtotal = selectedVariantPrice * quantity;
            const pmSel = document.getElementById('paymentMethod');
            const pmOpt = pmSel.selectedOptions[0];
            let fee = 0;
            if (pmOpt && pmOpt.value) {
                const rawFee = parseFloat(pmOpt.dataset.fee || 0);
                const feeType = pmOpt.dataset.feeType || 'fixed';
                fee = feeType === 'percent' ? (subtotal * rawFee / 100) : rawFee;
            }
            const discount = discountData ? discountData.discount : 0;
            const total = subtotal + fee - discount + uniqueCode;

            document.getElementById('calcSubtotal').textContent = fmt(subtotal);
            document.getElementById('calcFee').textContent = fmt(fee);
            document.getElementById('calcUnique').textContent = '+Rp ' + uniqueCode.toLocaleString('id');

            if (discount > 0) {
                document.getElementById('discountRow').style.display = 'flex';
                document.getElementById('calcDiscount').textContent = '−' + fmt(discount);
            } else {
                document.getElementById('discountRow').style.display = 'none';
            }
            document.getElementById('calcTotal').textContent = fmt(total);
        }

        async function validateDiscount() {
            const code = document.getElementById('discountCode').value.trim();
            if (!code) return;
            const subtotal = selectedVariantPrice * quantity;
            try {
                const res = await fetch('/api/discount/validate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    body: JSON.stringify({ code, amount: subtotal })
                });
                const data = await res.json();
                const msg = document.getElementById('discountMsg');
                if (data.valid) {
                    discountData = data;
                    msg.style.color = 'var(--primary)';
                    msg.textContent = '✅ Diskon Rp ' + Number(data.discount).toLocaleString('id') + ' berhasil diterapkan!';
                } else {
                    discountData = null;
                    msg.style.color = 'var(--red)';
                    msg.textContent = '❌ ' + data.message;
                }
                updateCalc();
            } catch (e) {
                toast('Gagal memvalidasi diskon', 'err');
            }
        }

        async function submitOrder() {
            const name = document.getElementById('customerName').value.trim();
            const phone = document.getElementById('customerPhone').value.trim();
            const pm = document.getElementById('paymentMethod').value;
            if (!name || !phone || !pm) { toast('Lengkapi nama, HP, dan metode pembayaran', 'err'); return; }

            const btn = document.getElementById('orderBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

            const payload = {
                customer_name: name,
                customer_phone: phone,
                customer_note: document.getElementById('customerNote').value,
                payment_method_id: pm,
                discount_code: discountData ? document.getElementById('discountCode').value : null,
                items: [{ product_id: {{ $product->id }}, variant_id: selectedVariantId, quantity }]
            };

            try {
                const res = await fetch('/api/order', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    closeModal('checkoutModal');
                    toast('Pesanan berhasil! Kode: ' + data.order_code);

                    // Langsung buka WA otomatis
                    setTimeout(() => {
                        window.open(data.whatsapp_link, '_blank');
                    }, 800);
                } else {
                    toast(data.message || 'Terjadi kesalahan', 'err');
                }
            } catch (e) {
                // Jika API error, fallback: buat pesan WA manual dari data frontend
                const subtotal = selectedVariantPrice * quantity;
                const pmSel = document.getElementById('paymentMethod');
                const pmName = pmSel.selectedOptions[0]?.textContent?.trim() || '-';
                const discount = discountData ? discountData.discount : 0;
                let fee = 0;
                const pmOpt = pmSel.selectedOptions[0];
                if (pmOpt && pmOpt.value) {
                    const rawFee = parseFloat(pmOpt.dataset.fee || 0);
                    const feeType = pmOpt.dataset.feeType || 'fixed';
                    fee = feeType === 'percent' ? (subtotal * rawFee / 100) : rawFee;
                }
                const total = subtotal + fee - discount + uniqueCode;
                const orderCode = 'ORD-' + Math.random().toString(36).substring(2, 8).toUpperCase();

                let waMsg = `Halo kak, saya mau pesan:\n\n`;
                waMsg += `📋 KODE PEMESANAN: ${orderCode}\n\n`;
                waMsg += `🛍️ Produk: {{ $product->name }}\n`;
                if (selectedVariantName) waMsg += `📦 Varian: ${selectedVariantName}\n`;
                waMsg += `📦 Jumlah: ${quantity}\n`;
                waMsg += `💰 Harga: ${fmt(subtotal)}\n`;
                if (discount > 0) waMsg += `🏷️ Diskon: -${fmt(discount)}\n`;
                if (fee > 0) waMsg += `📄 Biaya Admin: ${fmt(fee)}\n`;
                waMsg += `🔢 Kode Unik: +Rp ${uniqueCode.toLocaleString('id')}\n`;
                waMsg += `💵 Total: ${fmt(total)}\n`;
                waMsg += `💳 Pembayaran: ${pmName}\n`;
                waMsg += `📱 No. HP: ${phone}\n`;
                waMsg += `👤 Nama: ${name}\n`;

                const note = document.getElementById('customerNote').value.trim();
                if (note) waMsg += `📝 Catatan: ${note}\n`;

                const waNumber = '{{ preg_replace("/[^0-9]/", "", $setting->whatsapp_number ?? "") }}';
                const waLink = `https://wa.me/${waNumber}?text=${encodeURIComponent(waMsg)}`;

                closeModal('checkoutModal');
                toast('Mengarahkan ke WhatsApp...');
                setTimeout(() => window.open(waLink, '_blank'), 500);
            }

            btn.disabled = false;
            btn.innerHTML = '<i class="fab fa-whatsapp"></i> Kirim Pesanan via WhatsApp';
        }

        // Close modal on overlay click
        document.getElementById('checkoutModal').addEventListener('click', function (e) {
            if (e.target === this) closeModal('checkoutModal');
        });

        // ═══════ TESTIMONIAL ═══════
        let testiOrderCode = '';
        let testiRating = 0;

        async function validateOrderForTestimonial() {
            const code = document.getElementById('testiOrderCode').value.trim();
            if (!code) { toast('Masukkan kode pembelian', 'err'); return; }

            const btn = document.getElementById('testiValidateBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            try {
                const res = await fetch('/api/testimonial/validate-order', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    body: JSON.stringify({ order_code: code })
                });
                const data = await res.json();
                const msg = document.getElementById('testiCodeMsg');

                if (data.valid) {
                    testiOrderCode = data.order_code;
                    document.getElementById('testiVerifiedName').textContent = data.customer_name;
                    document.getElementById('testiVerifiedProduct').textContent = data.product_name;
                    document.getElementById('testiStep1').classList.remove('active');
                    document.getElementById('testiStep2').classList.add('active');
                    msg.textContent = '';
                } else {
                    msg.style.color = 'var(--red)';
                    msg.textContent = '❌ ' + data.message;
                }
            } catch (e) {
                toast('Gagal memverifikasi kode', 'err');
            }

            btn.disabled = false;
            btn.innerHTML = 'Verifikasi';
        }

        function setRating(val) {
            testiRating = val;
            document.querySelectorAll('#starInput .star-btn').forEach(btn => {
                btn.classList.toggle('active', parseInt(btn.dataset.val) <= val);
            });
        }

        // Character counter
        document.getElementById('testiMessage')?.addEventListener('input', function () {
            document.getElementById('testiCharCount').textContent = this.value.length;
        });

        async function submitTestimonial() {
            if (testiRating === 0) { toast('Pilih rating bintang', 'err'); return; }
            const content = document.getElementById('testiMessage').value.trim();
            if (!content) { toast('Tulis pesan ulasan', 'err'); return; }

            const btn = document.getElementById('testiSubmitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';

            try {
                const res = await fetch('/api/testimonial/submit', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    body: JSON.stringify({
                        order_code: testiOrderCode,
                        rating: testiRating,
                        content: content
                    })
                });
                const data = await res.json();

                if (data.success) {
                    document.getElementById('testiStep2').classList.remove('active');
                    document.getElementById('testiStep3').classList.add('active');
                    toast('Ulasan berhasil dikirim! 🎉');
                } else {
                    toast(data.message || 'Gagal mengirim ulasan', 'err');
                }
            } catch (e) {
                toast('Gagal mengirim ulasan', 'err');
            }

            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Ulasan';
        }
    </script>
@endsection
