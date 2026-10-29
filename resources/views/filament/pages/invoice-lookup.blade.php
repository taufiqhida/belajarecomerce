<x-filament-panels::page>
    <style>
        .il-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 1px 4px rgba(0,0,0,.05);
            margin-bottom: 1rem;
        }
        .il-search-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr auto;
            gap: .75rem;
            align-items: flex-end;
        }
        @media(max-width:900px){
            .il-search-grid { grid-template-columns: 1fr 1fr; }
        }
        @media(max-width:560px){
            .il-search-grid { grid-template-columns: 1fr; }
        }
        .il-field label {
            display: block;
            font-size: .72rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: .35rem;
        }
        .il-field input,
        .il-field select {
            width: 100%;
            height: 40px;
            padding: 0 .85rem;
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            font-size: .875rem;
            font-family: inherit;
            background: #fff;
            color: #111827;
            outline: none;
            transition: border .2s, box-shadow .2s;
        }
        .il-field input:focus,
        .il-field select:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16,185,129,.12);
        }
        .il-btn {
            height: 40px;
            padding: 0 1.25rem;
            background: linear-gradient(135deg,#10b981,#059669);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: .875rem;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: .4rem;
            transition: all .2s;
            font-family: inherit;
        }
        .il-btn:hover { filter: brightness(1.07); transform: translateY(-1px); }

        /* Stats */
        .il-stats { display: grid; grid-template-columns: repeat(4,1fr); gap: .75rem; margin-bottom: 1rem; }
        @media(max-width:700px){ .il-stats { grid-template-columns: repeat(2,1fr); } }
        .il-stat {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
        }
        .il-stat-label { font-size: .68rem; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: .06em; margin-bottom: .3rem; }
        .il-stat-val { font-size: 1.5rem; font-weight: 900; color: #111827; }
        .il-stat-val.green { color: #059669; }
        .il-stat-val.blue { color: #2563eb; }
        .il-stat-val.amber { color: #d97706; }

        /* Order card */
        .il-order {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,.05);
            margin-bottom: .75rem;
            transition: box-shadow .2s;
        }
        .il-order:hover { box-shadow: 0 4px 16px rgba(0,0,0,.09); }
        .il-order-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .75rem 1.25rem;
            background: #f9fafb;
            border-bottom: 1px solid #f0f0f0;
            flex-wrap: wrap;
            gap: .5rem;
        }
        .il-order-code { font-weight: 800; font-size: .9rem; color: #059669; }
        .il-order-date { font-size: .75rem; color: #9ca3af; }
        .il-order-actions { display: flex; align-items: center; gap: .5rem; }
        .il-badge {
            font-size: .68rem;
            font-weight: 700;
            padding: .2rem .65rem;
            border-radius: 999px;
        }
        .badge-completed { background: #d1fae5; color: #065f46; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-processing,.badge-confirmed { background: #dbeafe; color: #1e40af; }
        .badge-shipped { background: #ede9fe; color: #5b21b6; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }

        .il-action-btn {
            font-size: .72rem;
            font-weight: 700;
            padding: .3rem .75rem;
            border-radius: 6px;
            border: 1.5px solid;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            transition: all .15s;
            font-family: inherit;
            background: transparent;
        }
        .il-action-btn.nota { border-color: #a7f3d0; color: #065f46; background: #f0fdf4; }
        .il-action-btn.nota:hover { background: #d1fae5; }
        .il-action-btn.detail { border-color: #e5e7eb; color: #374151; background: #f9fafb; }
        .il-action-btn.detail:hover { background: #f3f4f6; }

        .il-order-body { padding: 1rem 1.25rem; }
        .il-order-meta { display: flex; flex-wrap: wrap; gap: 1.5rem; margin-bottom: .75rem; }
        .il-meta-label { font-size: .68rem; color: #9ca3af; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 2px; }
        .il-meta-val { font-size: .85rem; font-weight: 600; color: #111827; }
        .il-meta-sub { font-size: .75rem; color: #6b7280; }

        .il-items { background: #f9fafb; border-radius: 8px; padding: .75rem 1rem; margin-bottom: .75rem; }
        .il-item-row { display: flex; justify-content: space-between; font-size: .82rem; padding: .2rem 0; }
        .il-item-row:not(:last-child) { border-bottom: 1px dashed #e5e7eb; margin-bottom: .25rem; padding-bottom: .25rem; }
        .il-item-name { color: #374151; }
        .il-item-variant { font-size: .72rem; color: #9ca3af; }
        .il-item-price { font-weight: 700; color: #111827; white-space: nowrap; }

        .il-total-row { display: flex; justify-content: space-between; align-items: center; padding-top: .5rem; border-top: 1.5px solid #e5e7eb; }
        .il-total-label { font-size: .82rem; color: #6b7280; }
        .il-total-val { font-size: 1.1rem; font-weight: 900; color: #059669; }

        /* Empty state */
        .il-empty { text-align: center; padding: 4rem 1rem; color: #9ca3af; }
        .il-empty-icon { font-size: 3rem; margin-bottom: .75rem; }
        .il-empty-title { font-size: 1rem; font-weight: 700; color: #374151; margin-bottom: .25rem; }
        .il-empty-sub { font-size: .85rem; }
    </style>

    {{-- SEARCH FORM --}}
    <div class="il-card">
        <div style="font-size:.8rem;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.07em;margin-bottom:1rem;">
            🔍 Cari Pesanan Pelanggan
        </div>
        <div class="il-search-grid">
            <div class="il-field">
                <label>Nomor HP / WhatsApp</label>
                <input type="tel" wire:model="phone" wire:keydown.enter="search" placeholder="Contoh: 087739612610">
            </div>
            <div class="il-field">
                <label>Filter Status</label>
                <select wire:model="status">
                    <option value="semua">Semua Status</option>
                    <option value="proses">Dalam Proses</option>
                    <option value="selesai">Selesai</option>
                    <option value="batal">Dibatalkan</option>
                </select>
            </div>
            <div class="il-field">
                <label>Dari Tanggal</label>
                <input type="date" wire:model="from">
            </div>
            <div class="il-field">
                <label>Sampai Tanggal</label>
                <input type="date" wire:model="to">
            </div>
            <div>
                <button wire:click="search" class="il-btn">
                    🔍 Cari
                </button>
            </div>
            {{-- Cetak Semua button, only if searched --}}
            @if($this->searched && $this->orders->count() > 0)
            <div style="margin-top:.75rem;padding-top:.75rem;border-top:1px solid #e5e7eb;display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
                <a href="{{ route('admin.print-orders', array_filter([
                        'phone'  => $this->phone  ?: null,
                        'status' => $this->status !== 'semua' ? $this->status : null,
                        'from'   => $this->from   ?: null,
                        'to'     => $this->to     ?: null,
                    ])) }}"
                   target="_blank"
                   style="display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1.1rem;background:linear-gradient(135deg,#059669,#047857);color:#fff;font-size:.82rem;font-weight:700;border-radius:8px;text-decoration:none;transition:all .2s;"
                   onmouseover="this.style.filter='brightness(1.1)'" onmouseout="this.style.filter=''">
                    🖨️ Cetak Semua ({{ $this->orders->count() }} Pesanan)
                </a>
                <span style="font-size:.78rem;color:#9ca3af;">Tabel semua pesanan hasil filter, bisa disimpan jadi PDF</span>
            </div>
            @endif
        </div>
    </div>

    @if($this->searched)
        @php $orders = $this->orders; @endphp

        @if($orders->count() > 0)
            {{-- STATS --}}
            <div class="il-stats">
                <div class="il-stat">
                    <div class="il-stat-label">Total Pesanan</div>
                    <div class="il-stat-val">{{ $orders->count() }}</div>
                </div>
                <div class="il-stat">
                    <div class="il-stat-label">Selesai</div>
                    <div class="il-stat-val green">{{ $orders->where('status','completed')->count() }}</div>
                </div>
                <div class="il-stat">
                    <div class="il-stat-label">Dalam Proses</div>
                    <div class="il-stat-val blue">{{ $orders->whereIn('status',['pending','confirmed','processing','shipped'])->count() }}</div>
                </div>
                <div class="il-stat">
                    <div class="il-stat-label">Revenue (Selesai)</div>
                    <div class="il-stat-val green" style="font-size:1rem;">Rp {{ number_format($this->totalRevenue,0,',','.') }}</div>
                </div>
            </div>

            {{-- ORDER LIST --}}
            @foreach($orders as $order)
                @php
                    $badgeClass = match($order->status) {
                        'completed' => 'badge-completed',
                        'cancelled' => 'badge-cancelled',
                        'pending'   => 'badge-pending',
                        'shipped'   => 'badge-shipped',
                        default     => 'badge-processing',
                    };
                @endphp
                <div class="il-order">
                    <div class="il-order-head">
                        <div>
                            <div class="il-order-code">{{ $order->order_code }}</div>
                            <div class="il-order-date">{{ $order->ordered_at ? $order->ordered_at->format('d M Y, H:i') : '-' }}</div>
                        </div>
                        <div class="il-order-actions">
                            <span class="il-badge {{ $badgeClass }}">{{ $order->status_label }}</span>
                            <a href="{{ route('admin.nota', $order->id) }}" target="_blank" class="il-action-btn nota">
                                🖨️ Cetak Nota
                            </a>
                            <a href="{{ route('filament.admin.resources.orders.view', $order->id) }}" class="il-action-btn detail">
                                👁️ Detail
                            </a>
                        </div>
                    </div>
                    <div class="il-order-body">
                        <div class="il-order-meta">
                            <div>
                                <div class="il-meta-label">Pembeli</div>
                                <div class="il-meta-val">{{ $order->customer_name }}</div>
                                <div class="il-meta-sub">{{ $order->customer_phone }}</div>
                            </div>
                            <div>
                                <div class="il-meta-label">Metode Bayar</div>
                                <div class="il-meta-val">{{ $order->paymentMethod->name ?? '-' }}</div>
                            </div>
                            @if($order->customer_address)
                            <div>
                                <div class="il-meta-label">Alamat</div>
                                <div class="il-meta-val">{{ $order->customer_address }}</div>
                            </div>
                            @endif
                            @if($order->customer_note)
                            <div>
                                <div class="il-meta-label">Catatan</div>
                                <div class="il-meta-val">{{ $order->customer_note }}</div>
                            </div>
                            @endif
                        </div>

                        <div class="il-items">
                            @foreach($order->items as $item)
                                <div class="il-item-row">
                                    <div>
                                        <span class="il-item-name">{{ $item->product_name }}</span>
                                        @if($item->variant_name)
                                            <span class="il-item-variant"> · {{ $item->variant_name }}</span>
                                        @endif
                                        <span class="il-item-variant"> ×{{ $item->quantity }}</span>
                                    </div>
                                    <div class="il-item-price">Rp {{ number_format($item->subtotal,0,',','.') }}</div>
                                </div>
                            @endforeach
                        </div>

                        <div class="il-total-row">
                            <span class="il-total-label">Total Bayar</span>
                            <span class="il-total-val">Rp {{ number_format($order->total,0,',','.') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach

        @else
            <div class="il-empty">
                <div class="il-empty-icon">🔍</div>
                <div class="il-empty-title">Tidak ada pesanan ditemukan</div>
                <div class="il-empty-sub">
                    Untuk nomor <strong>{{ $this->phone }}</strong>
                    @if($this->status !== 'semua') dengan filter <strong>{{ $this->status }}</strong>@endif
                    @if($this->from || $this->to)
                        pada tanggal {{ $this->from ?: '...' }} s/d {{ $this->to ?: '...' }}
                    @endif
                </div>
            </div>
        @endif

    @else
        <div class="il-empty">
            <div class="il-empty-icon">🧾</div>
            <div class="il-empty-title">Masukkan nomor HP pelanggan</div>
            <div class="il-empty-sub">Bisa filter status dan tanggal juga, lalu klik Cari</div>
        </div>
    @endif
</x-filament-panels::page>
