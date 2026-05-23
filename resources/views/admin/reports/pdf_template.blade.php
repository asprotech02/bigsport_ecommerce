<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan BigSport</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
            font-size: 11px;
            line-height: 1.4;
        }
        .header {
            border-bottom: 3px solid #d90429;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .logo-container {
            float: left;
            width: 50%;
        }
        .logo-container img {
            height: 45px;
            object-fit: contain;
        }
        .report-info {
            float: right;
            width: 50%;
            text-align: right;
        }
        .report-info h1 {
            font-size: 20px;
            margin: 0;
            color: #d90429;
            font-weight: 800;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .report-info p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 10px;
        }
        .clear {
            clear: both;
        }
        .stats-grid {
            margin-bottom: 25px;
        }
        .stat-card {
            float: left;
            width: 22%;
            background-color: #f8f9fa;
            border-left: 3px solid #d90429;
            padding: 10px;
            margin-right: 2%;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .stat-card:last-child {
            margin-right: 0;
            width: 24%;
        }
        .stat-title {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .stat-val {
            font-size: 14px;
            font-weight: bold;
            color: #111;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #111;
            border-bottom: 1.5px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        th {
            background-color: #111;
            color: #fff;
            text-align: left;
            padding: 6px 8px;
            font-size: 10px;
            text-transform: uppercase;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        tr:nth-child(even) td {
            background-color: #fafafa;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            color: #fff;
        }
        .badge-success { background-color: #2ec4b6; }
        .badge-info { background-color: #00b4d8; }
        .badge-danger { background-color: #d90429; }
        .badge-warning { background-color: #ffb703; }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 8px;
            font-size: 8px;
            color: #aaa;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo-container">
            @if(!empty($logoBase64))
                <img src="{{ $logoBase64 }}" alt="BigSport Logo">
            @else
                <h2 style="margin:0; color:#d90429; font-weight:800; font-style:italic;">BIGSPORT</h2>
            @endif
        </div>
        <div class="report-info">
            <h1>Sales Report</h1>
            <p>Periode: <strong>{{ $startDate->format('d M Y') }}</strong> s/d <strong>{{ $endDate->format('d M Y') }}</strong></p>
            <p>Dicetak pada: {{ now()->format('d M Y H:i:s') }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Summary Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-title">Total Revenue (Paid)</div>
            <div class="stat-val" style="color: #2ec4b6;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Total Orders</div>
            <div class="stat-val">{{ number_format($totalOrders) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #2ec4b6;">
            <div class="stat-title">Completed Orders</div>
            <div class="stat-val" style="color: #2ec4b6;">{{ number_format($completedOrders) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #d90429;">
            <div class="stat-title">Cancelled Orders</div>
            <div class="stat-val" style="color: #d90429;">{{ number_format($cancelledOrders) }}</div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Detailed Sales List -->
    <div class="section-title">Rincian Transaksi Penjualan</div>
    <table>
        <thead>
            <tr>
                <th width="15%">No. Invoice</th>
                <th width="15%">Tanggal</th>
                <th width="20%">Nama Pelanggan</th>
                <th width="12%">Status Order</th>
                <th width="12%">Status Bayar</th>
                <th class="text-right" width="26%">Total Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td style="font-weight: bold; color: #d90429;">{{ $order->invoice_number }}</td>
                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                    <td>
                        {{ $order->user->name ?? 'Tamu' }}
                        <div style="font-size: 8px; color: #888;">{{ $order->user->email ?? '' }}</div>
                    </td>
                    <td>
                        <span class="badge {{ in_array(strtolower($order->status), ['completed', 'delivered', 'shipped']) ? 'badge-success' : (in_array(strtolower($order->status), ['cancelled', 'failed']) ? 'badge-danger' : 'badge-info') }}">
                            {{ strtoupper($order->status) }}
                        </span>
                    </td>
                    <td>
                        @php
                            $rawPayStatus = strtolower($order->payment_status);
                            $displayPayStatus = $rawPayStatus === 'settlement' ? 'paid' : $rawPayStatus;
                            $isPaySuccess = in_array($displayPayStatus, ['paid', 'success']);
                            $isPayFailed = in_array($displayPayStatus, ['failed', 'expire', 'expired', 'deny', 'cancel']);
                            $payBadgeClass = $isPaySuccess ? 'badge-success' : ($isPayFailed ? 'badge-danger' : 'badge-warning');
                        @endphp
                        <span class="badge {{ $payBadgeClass }}">
                            {{ strtoupper($displayPayStatus) }}
                        </span>
                    </td>
                    <td class="text-right" style="font-weight: bold;">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #aaa; padding: 20px;">Tidak ada data transaksi untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Best Seller Section -->
    <div style="width: 100%; margin-top: 10px;">
        <div style="float: left; width: 48%;">
            <div class="section-title">Top 5 Produk Terlaris</div>
            <table style="margin-bottom:0;">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th class="text-right" width="30%">Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bestSellers as $product)
                        <tr>
                            <td style="font-weight:bold;">{{ $product->name }}</td>
                            <td class="text-right" style="font-weight:bold; color: #d90429;">{{ $product->total_sold }} item</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align: center; color: #aaa;">Belum ada produk terjual.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div style="float: right; width: 48%;">
            <div class="section-title">Ringkasan Metode Pembayaran</div>
            <table style="margin-bottom:0;">
                <thead>
                    <tr>
                        <th>Tipe Pembayaran</th>
                        <th class="text-right">Total Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $paymentSummary = \Illuminate\Support\Facades\DB::table('payments')
                            ->join('orders', 'payments.order_id', '=', 'orders.id')
                            ->select('payments.payment_type', \Illuminate\Support\Facades\DB::raw('SUM(payments.gross_amount) as total'))
                            ->whereIn('orders.payment_status', ['paid', 'settlement'])
                            ->whereBetween('orders.created_at', [$startDate, $endDate])
                            ->groupBy('payments.payment_type')
                            ->get();
                    @endphp
                    @forelse($paymentSummary as $ps)
                        <tr>
                            <td style="font-weight:bold; text-transform:uppercase;">{{ str_replace('_', ' ', $ps->payment_type) }}</td>
                            <td class="text-right" style="font-weight:bold;">Rp {{ number_format($ps->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align: center; color: #aaa;">Belum ada ringkasan pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="clear"></div>
    </div>

    <div class="footer">
        BigSport Premium E-commerce Admin Command Center &copy; {{ date('Y') }}
    </div>

</body>
</html>
