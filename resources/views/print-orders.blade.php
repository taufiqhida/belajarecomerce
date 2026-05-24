<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Pesanan — {{ $setting->store_name ?? 'Cold Storage' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            background: #f5f5f5;
            color: #111;
            padding: 20px;
        }

        .page-header {
            max-width: 860px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .store-title {
            font-size: 20px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #059669;
        }

        .report-meta {
            text-align: right;
            font-size: 11px;
            color: #555;
            line-height: 1.7;
        }

        /* Filter info bar */
        .filter-bar {
            max-width: 860px;
            margin: 0 auto 16px;
            background: #f0fdf4;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            padding: .6rem 1rem;
            font-size: 11px;
            color: #065f46;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .filter-item {
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        .filter-label {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            color: #059669;
        }

        /* Summary */
        .summary-grid {
            max-width: 860px;
            margin: 0 auto 16px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: .5rem;
        }

        .summary-box {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: .75rem 1rem;
            text-align: center;
        }

        .s-label {
            font-size: 10px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 4px;
            font-weight: 700;
        }

        .s-val {
            font-size: 1.3rem;
            font-weight: 900;
            color: #111;
        }

        .s-val.green {
            color: #059669;
        }

        .s-val.blue {
            color: #2563eb;
        }

        /* Table */
        .wrap {
            max-width: 860px;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .07);
        }

        thead {
            background: #059669;
            color: #fff;
        }

        thead th {
            padding: .6rem .85rem;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        tbody tr:hover {
            background: #f0fdf4;
        }

        td {
            padding: .55rem .85rem;
            border-bottom: 1px solid #f0f0f0;
            font-size: 11.5px;
            vertical-align: top;
        }

        td:last-child {
            text-align: right;
            font-weight: 700;
        }

        .td-code {
            font-weight: 800;
            color: #059669;
            font-size: 11px;
        }

        .td-date {
            color: #6b7280;
            font-size: 10.5px;
        }

        .td-items {
            font-size: 10.5px;
            color: #374151;
            line-height: 1.5;
        }

        .td-item-variant {
            color: #9ca3af;
            font-size: 10px;
        }

        .badge {
            display: inline-block;
            font-size: 9.5px;
            font-weight: 700;
            padding: .15rem .55rem;
            border-radius: 999px;
        }

        .badge-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-proses {
            background: #dbeafe;
            color: #1e40af;
        }

        /* Grand Total */
        tfoot td {
            font-weight: 800;
            font-size: 12px;
            padding: .7rem .85rem;
            background: #f0fdf4;
            border-top: 2px solid #059669;
        }

        tfoot td:last-child {
            color: #059669;
            font-size: 14px;
        }

        /* Print controls */
        .control-bar {
            max-width: 860px;
            margin: 0 auto 14px;
            display: flex;
            gap: .75rem;
        }

        .btn-print {
            padding: .55rem 1.25rem;
            background: #059669;
            color: #fff;
            border: none;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-back {
            padding: .55rem 1.25rem;
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        /* Print styles */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .control-bar {
                display: none !important;
            }

            table {
                box-shadow: none;
            }
        }
    </style>
</head>

<body>

    {{-- Controls --}}
    <div class="control-bar no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
        <a href="javascript:history.back()" class="btn-back">← Kembali</a>
    </div>

    {{-- Header --}}
    <div class="page-header">
        <div>
            <div class="store-title">{{ $setting->store_name ?? 'Cold Storage' }}</div>
            <div style="font-size:11px;color:#6b7280;margin-top:2px;">Laporan Pesanan</div>
        </div>
        <div class="report-meta">
            Dicetak: {{ now()->format('d M Y, H:i') }}<br>
            Total Pesanan: <strong>{{ $orders->count() }}</strong>
        </div>
    </div>

    {{-- Filter info --}}
    <div class="filter-bar">
        @if($request->filled('phone'))
            <div class="filter-item"><span class="filter-label">HP:</span> {{ $request->phone }}</div>
        @endif
        @if($request->filled('status') && $request->status !== 'semua')
            <div class="filter-item"><span class="filter-label">Status:</span> {{ ucfirst($request->status) }}</div>
        @endif
        @if($request->filled('from'))
            <div class="filter-item"><span class="filter-label">Dari:</span>
                {{ \Carbon\Carbon::parse($request->from)->format('d M Y') }}</div>
        @endif
        @if($request->filled('to'))
            <div class="filter-item"><span class="filter-label">Sampai:</span>
                {{ \Carbon\Carbon::parse($request->to)->format('d M Y') }}</div>
        @endif
    </div>

    {{-- Summary --}}
    <div class="summary-grid">
        <div class="summary-box">
            <div class="s-label">Total Pesanan</div>
            <div class="s-val">{{ $orders->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="s-label">Selesai</div>
            <div class="s-val green">{{ $orders->where('status', 'completed')->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="s-label">Dalam Proses</div>
            <div class="s-val blue">
                {{ $orders->whereIn('status', ['pending', 'confirmed', 'processing', 'shipped'])->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="s-label">Revenue (Selesai)</div>
            <div class="s-val green" style="font-size:.95rem">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kode & Tanggal</th>
                    <th>Pembeli</th>
                    <th>Item Pesanan</th>
                    <th>Status</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    @php
                        switch ($order->status) {
                            case 'completed': $badgeClass = 'badge-completed'; break;
                            case 'cancelled': $badgeClass = 'badge-cancelled'; break;
                            case 'pending':   $badgeClass = 'badge-pending';   break;
                            default:          $badgeClass = 'badge-proses';    break;
                        }
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="td-code">{{ $order->order_code }}</div>
                            <div class="td-date">{{ $order->ordered_at ? $order->ordered_at->format('d/m/Y H:i') : '-' }}
                            </div>
                        </td>
                        <td>
                            <div style="font-weight:600">{{ $order->customer_name }}</div>
                            <div style="color:#9ca3af;font-size:10px">{{ $order->customer_phone }}</div>
                        </td>
                        <td class="td-items">
                            @foreach($order->items as $item)
                                <div>
                                    {{ $item->product_name }}
                                    @if($item->variant_name)
                                        <span class="td-item-variant">({{ $item->variant_name }})</span>
                                    @endif
                                    ×{{ $item->quantity }}
                                </div>
                            @endforeach
                        </td>
                        <td><span class="badge {{ $badgeClass }}">{{ $order->status_label }}</span></td>
                        <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:2rem;color:#9ca3af">Tidak ada pesanan</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align:right">TOTAL (Pesanan Selesai)</td>
                    <td>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        if (new URLSearchParams(window.location.search).get('autoprint') === '1') {
            window.onload = () => window.print();
        }
    </script>
</body>

</html>
