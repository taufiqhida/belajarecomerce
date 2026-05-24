<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota #{{ $order->order_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        .nota {
            background: #fff;
            width: 300px;
            padding: 20px 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .15);
        }

        /* Header */
        .nota-header {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #333;
        }

        .store-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .store-tagline {
            font-size: 10px;
            color: #555;
            margin-top: 2px;
        }

        /* Order info */
        .nota-info {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #333;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 11px;
        }

        .info-label {
            color: #555;
        }

        .info-value {
            font-weight: bold;
        }

        /* Customer */
        .nota-customer {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #333;
        }

        .customer-label {
            font-size: 10px;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .customer-name {
            font-weight: bold;
            font-size: 12px;
        }

        .customer-phone {
            font-size: 11px;
            color: #555;
        }

        /* Items */
        .nota-items {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #333;
        }

        .items-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #777;
            margin-bottom: 6px;
        }

        .item {
            margin-bottom: 6px;
        }

        .item-name {
            font-weight: bold;
            font-size: 11px;
        }

        .item-variant {
            font-size: 10px;
            color: #666;
        }

        .item-calc {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #444;
            margin-top: 1px;
        }

        /* Summary */
        .nota-summary {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #333;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 3px;
        }

        .summary-label {
            color: #555;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            font-weight: bold;
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px solid #333;
        }

        /* Payment */
        .nota-payment {
            text-align: center;
            font-size: 11px;
            color: #555;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #333;
        }

        /* Footer */
        .nota-footer {
            text-align: center;
        }

        .footer-thanks {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .footer-sub {
            font-size: 10px;
            color: #666;
            line-height: 1.5;
        }

        .footer-code {
            font-size: 10px;
            color: #aaa;
            margin-top: 8px;
            letter-spacing: 1px;
        }

        /* Print */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .nota {
                box-shadow: none;
                width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }

        /* Print button */
        .print-btn {
            display: block;
            width: 300px;
            margin: 16px auto 0;
            padding: 10px;
            background: #16a34a;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            font-family: inherit;
            text-align: center;
        }

        .print-btn:hover {
            background: #15803d;
        }
    </style>
</head>

<body>

    <div class="nota">

        {{-- HEADER --}}
        <div class="nota-header">
            <div class="store-name">{{ $setting->store_name ?? 'Cold Storage' }}</div>
            <div class="store-tagline">{{ $setting->store_description ?? 'Terima kasih telah berbelanja!' }}</div>
        </div>

        {{-- INFO NOTA --}}
        <div class="nota-info">
            <div class="info-row">
                <span class="info-label">No. Nota</span>
                <span class="info-value">{{ $order->order_code }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal</span>
                <span class="info-value">{{ $order->ordered_at ? $order->ordered_at->format('d/m/Y H:i') : '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value">{{ $order->status_label }}</span>
            </div>
        </div>

        {{-- CUSTOMER --}}
        <div class="nota-customer">
            <div class="customer-label">Pembeli</div>
            <div class="customer-name">{{ $order->customer_name }}</div>
            <div class="customer-phone">{{ $order->customer_phone }}</div>
        </div>

        {{-- ITEMS --}}
        <div class="nota-items">
            <div class="items-title">Item Pesanan</div>
            @foreach ($order->items as $item)
                <div class="item">
                    <div class="item-name">{{ $item->product_name }}</div>
                    @if($item->variant_name)
                        <div class="item-variant">Varian: {{ $item->variant_name }}</div>
                    @endif
                    <div class="item-calc">
                        <span>{{ $item->quantity }}x @ Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- SUMMARY --}}
        <div class="nota-summary">
            <div class="summary-row">
                <span class="summary-label">Subtotal</span>
                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($order->admin_fee > 0)
                <div class="summary-row">
                    <span class="summary-label">Biaya Admin</span>
                    <span>Rp {{ number_format($order->admin_fee, 0, ',', '.') }}</span>
                </div>
            @endif
            @if($order->discount_amount > 0)
                <div class="summary-row">
                    <span class="summary-label">Diskon</span>
                    <span>-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            @if($order->unique_code > 0)
                <div class="summary-row">
                    <span class="summary-label">Kode Unik</span>
                    <span>+Rp {{ number_format($order->unique_code, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="total-row">
                <span>TOTAL</span>
                <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- PAYMENT --}}
        <div class="nota-payment">
            Dibayar via: <strong>{{ $order->paymentMethod->name ?? '-' }}</strong>
            @if($order->customer_note)
                <br>Catatan: {{ $order->customer_note }}
            @endif
        </div>

        {{-- FOOTER --}}
        <div class="nota-footer">
            <div class="footer-thanks">Terima Kasih! 🙏</div>
            <div class="footer-sub">
                Simpan nota ini sebagai bukti pembelian.<br>
                Barang yang sudah dibeli tidak dapat dikembalikan.
            </div>
            <div class="footer-code">{{ $order->order_code }}</div>
        </div>

    </div>

    <button class="print-btn no-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>

    <script>
        // Auto print jika ada query ?autoprint=1
        if (new URLSearchParams(window.location.search).get('autoprint') === '1') {
            window.onload = () => window.print();
        }
    </script>
</body>

</html>
