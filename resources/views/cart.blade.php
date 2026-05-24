@extends('layouts.app')

@section('title', 'Keranjang - ' . ($setting->store_name ?? 'Cold Storage'))

@section('content')
    <div class="cart-page-wrap">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.75rem;">
            <h2 style="font-size:1.5rem;font-weight:800;">🛒 Keranjang Belanja</h2>
            <a href="{{ route('home') }}"
                style="font-size:0.875rem;color:var(--mid);font-weight:600;display:flex;align-items:center;gap:.35rem;">
                <i class="fas fa-arrow-left" style="font-size:.75rem;"></i> Lanjut Belanja
            </a>
        </div>

        {{-- Empty State --}}
        <div class="cart-empty" id="cartEmpty">
            <div class="cart-empty-icon">🛒</div>
            <h3 style="font-size:1.15rem;font-weight:700;color:var(--dark);margin-bottom:0.5rem;">Keranjang masih kosong
            </h3>
            <p style="color:var(--muted);margin-bottom:1.75rem;">Yuk, temukan produk favoritmu di katalog kami!</p>
            <a href="{{ route('home') }}" class="btn btn-primary">Lihat Katalog</a>
        </div>

        {{-- Cart Items --}}
        <div id="cartContent" style="display:none;">
            <table class="cart-table" id="cartTable">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="cartRows"></tbody>
            </table>

            <div class="cart-summary-box">
                <div class="sum-row"><span style="color:var(--mid)">Total Item</span><span id="sumItems">0</span></div>
                <div class="sum-row total">
                    <span>Total Harga</span>
                    <span class="sum-amt" id="sumTotal">Rp 0</span>
                </div>

                <div style="display:flex;gap:.75rem;margin-top:1.25rem;">
                    <a href="{{ route('home') }}" class="btn btn-ghost btn-lg" style="flex:1;">
                        🛍️ Belanja Lagi
                    </a>
                    <button onclick="openCartCheckout()" class="btn btn-wa btn-lg" style="flex:1;">
                        <i class="fab fa-whatsapp"></i> Checkout via WA
                    </button>
                </div>

                <button onclick="if(confirm('Yakin kosongkan keranjang?')){clearCart();renderCart();}" class="btn btn-sm"
                    style="width:100%;margin-top:.75rem;background:none;color:var(--muted);font-size:.78rem;">
                    <i class="fas fa-trash" style="font-size:.7rem;"></i> Kosongkan Keranjang
                </button>
            </div>
        </div>
    </div>

    {{-- CHECKOUT MODAL --}}
    <div class="overlay" id="cartCheckoutModal">
        <div class="modal" style="max-width:560px;">
            <div class="modal-header">
                <div class="modal-title">🛒 Checkout Keranjang</div>
                <button class="modal-close" onclick="closeModal('cartCheckoutModal')">✕</button>
            </div>

            {{-- Order Summary --}}
            <div
                style="background:var(--light);border-radius:12px;padding:1rem;margin-bottom:1.25rem;border:1px solid var(--border-2);max-height:180px;overflow-y:auto;">
                <div style="font-weight:700;margin-bottom:.5rem;font-size:.88rem;">Ringkasan Pesanan</div>
                <div id="checkoutItemsList" style="font-size:.82rem;color:var(--mid);"></div>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" class="form-control" id="cartCustName" placeholder="John Doe">
            </div>
            <div class="form-group">
                <label class="form-label">Nomor HP / WhatsApp *</label>
                <input type="tel" class="form-control" id="cartCustPhone" placeholder="08123456789">
            </div>
            <div class="form-group">
                <label class="form-label">Metode Pembayaran *</label>
                <select class="form-control" id="cartPaymentMethod">
                    <option value="">-- Pilih Metode --</option>
                    @foreach($paymentMethods as $pm)
                        <option value="{{ $pm->id }}" data-fee="{{ $pm->admin_fee }}" data-fee-type="{{ $pm->fee_type }}"
                            data-name="{{ $pm->name }} ({{ ucfirst($pm->type) }})">
                            {{ $pm->name }} ({{ ucfirst($pm->type) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Kode Diskon (opsional)</label>
                <div class="discount-row">
                    <input type="text" class="form-control" id="cartDiscountCode" placeholder="PROMO10"
                        style="text-transform:uppercase;">
                    <button class="btn btn-ghost" onclick="cartValidateDiscount()" style="white-space:nowrap;">Cek</button>
                </div>
                <div id="cartDiscountMsg" style="font-size:0.75rem;margin-top:0.35rem;"></div>
            </div>
            <div class="form-group">
                <label class="form-label">Catatan (opsional)</label>
                <textarea class="form-control" id="cartCustNote" placeholder="Permintaan khusus..."></textarea>
            </div>

            {{-- Price Summary --}}
            <div
                style="background:var(--light);border-radius:12px;padding:1rem;margin-bottom:1.25rem;font-size:0.875rem;border:1px solid var(--border-2);">
                <div style="display:flex;justify-content:space-between;margin-bottom:0.3rem;"><span
                        style="color:var(--mid)">Subtotal</span><span id="cartCalcSubtotal">Rp 0</span></div>
                <div style="display:flex;justify-content:space-between;margin-bottom:0.3rem;"><span
                        style="color:var(--mid)">Biaya Admin</span><span id="cartCalcFee">Rp 0</span></div>
                <div style="display:flex;justify-content:space-between;margin-bottom:0.3rem;display:none;"
                    id="cartDiscountRow"><span style="color:var(--mid)">Diskon</span><span id="cartCalcDiscount"
                        style="color:var(--primary);">-Rp 0</span></div>
                <div style="display:flex;justify-content:space-between;margin-bottom:0.3rem;"><span
                        style="color:var(--mid)">Kode Unik</span><span id="cartCalcUnique">+Rp 0</span></div>
                <div
                    style="display:flex;justify-content:space-between;font-weight:800;font-size:1rem;padding-top:0.6rem;border-top:1.5px solid var(--border);margin-top:0.4rem;">
                    <span>TOTAL</span><span id="cartCalcTotal" style="color:var(--primary-d);">Rp 0</span>
                </div>
            </div>

            <button class="btn btn-wa btn-block" style="font-size:1rem;" id="cartOrderBtn" onclick="cartSubmitOrder()">
                <i class="fab fa-whatsapp"></i> Kirim Pesanan via WhatsApp
            </button>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let cartUniqueCode = 0;
        let cartDiscountData = null;

        const fmtRp = n => 'Rp ' + Math.round(n).toLocaleString('id');

        function renderCart() {
            const cart = getCart();
            const empty = document.getElementById('cartEmpty');
            const content = document.getElementById('cartContent');

            if (cart.length === 0) {
                empty.style.display = 'block';
                content.style.display = 'none';
                return;
            }

            empty.style.display = 'none';
            content.style.display = 'block';

            let html = '';
            let total = 0;
            let totalItems = 0;

            cart.forEach((item, i) => {
                const sub = item.price * (item.qty || 1);
                total += sub;
                totalItems += (item.qty || 1);
                html += `
                    <tr>
                        <td>
                            <div class="cart-prod">
                                ${item.image
                        ? `<img src="${item.image}" alt="" class="cart-prod-img">`
                        : `<div class="cart-prod-img" style="display:flex;align-items:center;justify-content:center;font-size:1.4rem;">🛍️</div>`
                    }
                                <div>
                                    <div class="cart-prod-name">${item.name}</div>
                                    ${item.variant ? `<div class="cart-prod-var">${item.variant}</div>` : ''}
                                </div>
                            </div>
                        </td>
                        <td class="price-cell">Rp ${Number(item.price).toLocaleString('id')}</td>
                        <td>
                            <div class="qty-ctrl">
                                <button class="qty-btn" onclick="changeQty(${i}, -1)">−</button>
                                <span class="qty-num">${item.qty || 1}</span>
                                <button class="qty-btn" onclick="changeQty(${i}, 1)">+</button>
                            </div>
                        </td>
                        <td class="price-cell">Rp ${Number(sub).toLocaleString('id')}</td>
                        <td><button class="cart-remove" onclick="removeItem(${i})"><i class="fas fa-trash"></i></button></td>
                    </tr>`;
            });

            document.getElementById('cartRows').innerHTML = html;
            document.getElementById('sumItems').textContent = totalItems + ' item';
            document.getElementById('sumTotal').textContent = 'Rp ' + total.toLocaleString('id');
        }

        function changeQty(i, delta) {
            const cart = getCart();
            cart[i].qty = Math.max(1, (cart[i].qty || 1) + delta);
            saveCart(cart);
            renderCart();
        }

        function removeItem(i) {
            const cart = getCart();
            cart.splice(i, 1);
            saveCart(cart);
            renderCart();
        }

        // ═══ CHECKOUT MODAL ═══
        function openCartCheckout() {
            const cart = getCart();
            if (cart.length === 0) { toast('Keranjang masih kosong', 'err'); return; }

            // Generate kode unik
            cartUniqueCode = Math.floor(Math.random() * 999) + 1;
            cartDiscountData = null;
            document.getElementById('cartDiscountMsg').textContent = '';
            document.getElementById('cartDiscountCode').value = '';

            // Render item list
            let listHtml = '';
            cart.forEach(item => {
                listHtml += `<div style="display:flex;justify-content:space-between;padding:.25rem 0;border-bottom:1px solid var(--border-2);">`;
                listHtml += `<span>${item.name}${item.variant ? ' <small style="color:var(--muted)">(' + item.variant + ')</small>' : ''} × ${item.qty || 1}</span>`;
                listHtml += `<span style="font-weight:600;">${fmtRp(item.price * (item.qty || 1))}</span>`;
                listHtml += `</div>`;
            });
            document.getElementById('checkoutItemsList').innerHTML = listHtml;

            cartUpdateCalc();
            document.getElementById('cartCheckoutModal').classList.add('open');
        }

        function closeModal(id) { document.getElementById(id).classList.remove('open'); }

        document.getElementById('cartPaymentMethod').addEventListener('change', cartUpdateCalc);

        function cartGetSubtotal() {
            return getCart().reduce((sum, item) => sum + (item.price * (item.qty || 1)), 0);
        }

        function cartUpdateCalc() {
            const subtotal = cartGetSubtotal();
            const pmSel = document.getElementById('cartPaymentMethod');
            const pmOpt = pmSel.selectedOptions[0];
            let fee = 0;
            if (pmOpt && pmOpt.value) {
                const rawFee = parseFloat(pmOpt.dataset.fee || 0);
                const feeType = pmOpt.dataset.feeType || 'fixed';
                fee = feeType === 'percent' ? (subtotal * rawFee / 100) : rawFee;
            }
            const discount = cartDiscountData ? cartDiscountData.discount : 0;
            const total = subtotal + fee - discount + cartUniqueCode;

            document.getElementById('cartCalcSubtotal').textContent = fmtRp(subtotal);
            document.getElementById('cartCalcFee').textContent = fmtRp(fee);
            document.getElementById('cartCalcUnique').textContent = '+Rp ' + cartUniqueCode.toLocaleString('id');

            if (discount > 0) {
                document.getElementById('cartDiscountRow').style.display = 'flex';
                document.getElementById('cartCalcDiscount').textContent = '−' + fmtRp(discount);
            } else {
                document.getElementById('cartDiscountRow').style.display = 'none';
            }
            document.getElementById('cartCalcTotal').textContent = fmtRp(total);
        }

        async function cartValidateDiscount() {
            const code = document.getElementById('cartDiscountCode').value.trim();
            if (!code) return;
            const subtotal = cartGetSubtotal();
            try {
                const res = await fetch('/api/discount/validate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    body: JSON.stringify({ code, amount: subtotal })
                });
                const data = await res.json();
                const msg = document.getElementById('cartDiscountMsg');
                if (data.valid) {
                    cartDiscountData = data;
                    msg.style.color = 'var(--primary)';
                    msg.textContent = '✅ Diskon ' + fmtRp(data.discount) + ' berhasil!';
                } else {
                    cartDiscountData = null;
                    msg.style.color = 'var(--red)';
                    msg.textContent = '❌ ' + data.message;
                }
                cartUpdateCalc();
            } catch (e) {
                toast('Gagal memvalidasi diskon', 'err');
            }
        }

        async function cartSubmitOrder() {
            const name = document.getElementById('cartCustName').value.trim();
            const phone = document.getElementById('cartCustPhone').value.trim();
            const pm = document.getElementById('cartPaymentMethod').value;
            if (!name || !phone || !pm) { toast('Lengkapi nama, HP, dan metode pembayaran', 'err'); return; }

            const cart = getCart();
            if (cart.length === 0) { toast('Keranjang kosong', 'err'); return; }

            const btn = document.getElementById('cartOrderBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

            // Build items for API
            const items = cart.map(item => ({
                product_id: item.id,
                variant_id: item.variantId || null,
                quantity: item.qty || 1,
            }));

            const payload = {
                customer_name: name,
                customer_phone: phone,
                customer_note: document.getElementById('cartCustNote').value,
                payment_method_id: pm,
                discount_code: cartDiscountData ? document.getElementById('cartDiscountCode').value : null,
                items: items
            };

            try {
                const res = await fetch('/api/order', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    closeModal('cartCheckoutModal');
                    clearCart();
                    renderCart();
                    toast('Pesanan berhasil! Kode: ' + data.order_code);
                    setTimeout(() => window.open(data.whatsapp_link, '_blank'), 800);
                    return;
                } else {
                    toast(data.message || 'Terjadi kesalahan', 'err');
                }
            } catch (e) {
                // Fallback: buat pesan WA manual langsung
                const subtotal = cartGetSubtotal();
                const pmSel = document.getElementById('cartPaymentMethod');
                const pmName = pmSel.selectedOptions[0]?.dataset?.name || '-';
                const discount = cartDiscountData ? cartDiscountData.discount : 0;
                let fee = 0;
                const pmOpt = pmSel.selectedOptions[0];
                if (pmOpt && pmOpt.value) {
                    const rawFee = parseFloat(pmOpt.dataset.fee || 0);
                    const feeType = pmOpt.dataset.feeType || 'fixed';
                    fee = feeType === 'percent' ? (subtotal * rawFee / 100) : rawFee;
                }
                const total = subtotal + fee - discount + cartUniqueCode;
                const orderCode = 'ORD-' + Math.random().toString(36).substring(2, 8).toUpperCase();

                let waMsg = `Halo kak, saya mau pesan:\n\n`;
                waMsg += `📋 KODE PEMESANAN: ${orderCode}\n\n`;

                cart.forEach(item => {
                    waMsg += `🛍️ ${item.name}`;
                    if (item.variant) waMsg += ` (${item.variant})`;
                    waMsg += ` × ${item.qty || 1} = ${fmtRp(item.price * (item.qty || 1))}\n`;
                });

                waMsg += `\n💰 Subtotal: ${fmtRp(subtotal)}\n`;
                if (fee > 0) waMsg += `📄 Biaya Admin: ${fmtRp(fee)}\n`;
                if (discount > 0) waMsg += `🏷️ Diskon: -${fmtRp(discount)}\n`;
                waMsg += `🔢 Kode Unik: +Rp ${cartUniqueCode.toLocaleString('id')}\n`;
                waMsg += `💵 Total: ${fmtRp(total)}\n`;
                waMsg += `💳 Pembayaran: ${pmName}\n`;
                waMsg += `📱 No. HP: ${phone}\n`;
                waMsg += `👤 Nama: ${name}\n`;

                const note = document.getElementById('cartCustNote').value.trim();
                if (note) waMsg += `📝 Catatan: ${note}\n`;

                const waNumber = '{{ preg_replace("/[^0-9]/", "", $setting->whatsapp_number ?? "") }}';
                const waLink = `https://wa.me/${waNumber}?text=${encodeURIComponent(waMsg)}`;

                closeModal('cartCheckoutModal');
                clearCart();
                renderCart();
                toast('Mengarahkan ke WhatsApp...');
                setTimeout(() => window.open(waLink, '_blank'), 500);
                return;
            }

            btn.disabled = false;
            btn.innerHTML = '<i class="fab fa-whatsapp"></i> Kirim Pesanan via WhatsApp';
        }

        // Close modal on overlay click
        document.getElementById('cartCheckoutModal').addEventListener('click', function (e) {
            if (e.target === this) closeModal('cartCheckoutModal');
        });

        renderCart();
    </script>
@endsection
