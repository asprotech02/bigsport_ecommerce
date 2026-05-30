<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1a1a1a;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>

    <!-- Header Table -->
    <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td colspan="3" style="text-align: left; vertical-align: middle;">
                <h1 style="margin: 0; color: #d90429; font-size: 22pt; font-weight: 800; font-style: italic;">BIGSPORT</h1>
                <span style="font-size: 9pt; color: #888888;">Premium E-Commerce Admin Center</span>
            </td>
            <td colspan="3" style="text-align: right; vertical-align: middle;">
                <h2 style="margin: 0; color: #d90429; font-size: 16pt; font-weight: 800; text-transform: uppercase;">SALES REPORT</h2>
                <span style="font-size: 10pt; font-weight: bold; color: #555555;">
                    Periode: {{ $startDate->format('d M Y') }} s/d {{ $endDate->format('d M Y') }} | Status: {{ strtoupper($status ?? 'Semua') }}
                </span>
                <br>
                <span style="font-size: 8pt; color: #888888;">Dicetak pada: {{ now()->format('d M Y H:i:s') }}</span>
            </td>
        </tr>
    </table>

    <!-- Divider -->
    <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
        <tr>
            <td colspan="6" style="border-bottom: 4px solid #d90429; height: 10px;"></td>
        </tr>
        <tr>
            <td colspan="6" style="height: 15px;"></td>
        </tr>
    </table>

    <!-- Summary Cards -->
    <table border="0" cellpadding="10" cellspacing="0" style="width: 100%; margin-bottom: 25px;">
        <tr>
            <!-- Card 1: Revenue -->
            <td colspan="2" style="background-color: #f8f9fa; border-left: 4px solid #2ec4b6; border-top: 1px solid #e0e0e0; border-bottom: 1px solid #e0e0e0; border-right: 15px solid #ffffff;">
                <div style="font-size: 9pt; color: #888888; text-transform: uppercase; font-weight: bold;">Total Revenue (Paid)</div>
                <div style="font-size: 14pt; font-weight: bold; color: #2ec4b6; margin-top: 5px;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </td>
            <!-- Card 2: Total Orders -->
            <td colspan="1" style="background-color: #f8f9fa; border-left: 4px solid #d90429; border-top: 1px solid #e0e0e0; border-bottom: 1px solid #e0e0e0; border-right: 15px solid #ffffff;">
                <div style="font-size: 9pt; color: #888888; text-transform: uppercase; font-weight: bold;">Total Orders</div>
                <div style="font-size: 14pt; font-weight: bold; color: #111111; margin-top: 5px;">{{ number_format($totalOrders) }}</div>
            </td>
            <!-- Card 3: Completed Orders -->
            <td colspan="1" style="background-color: #f8f9fa; border-left: 4px solid #2ec4b6; border-top: 1px solid #e0e0e0; border-bottom: 1px solid #e0e0e0; border-right: 15px solid #ffffff;">
                <div style="font-size: 9pt; color: #888888; text-transform: uppercase; font-weight: bold;">Completed</div>
                <div style="font-size: 14pt; font-weight: bold; color: #2ec4b6; margin-top: 5px;">{{ number_format($completedOrders) }}</div>
            </td>
            <!-- Card 4: Cancelled Orders -->
            <td colspan="2" style="background-color: #f8f9fa; border-left: 4px solid #d90429; border-top: 1px solid #e0e0e0; border-bottom: 1px solid #e0e0e0; border-right: 1px solid #e0e0e0;">
                <div style="font-size: 9pt; color: #888888; text-transform: uppercase; font-weight: bold;">Cancelled</div>
                <div style="font-size: 14pt; font-weight: bold; color: #d90429; margin-top: 5px;">{{ number_format($cancelledOrders) }}</div>
            </td>
        </tr>
    </table>

    <!-- Blank Row -->
    <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
        <tr>
            <td colspan="6" style="height: 15px;"></td>
        </tr>
    </table>

    <!-- Section: Rincian Transaksi -->
    <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 10px;">
        <tr>
            <td colspan="6" style="font-size: 12pt; font-weight: bold; color: #111111; border-bottom: 2px solid #eeeeee; padding-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px;">
                Rincian Transaksi Penjualan
            </td>
        </tr>
        <tr>
            <td colspan="6" style="height: 10px;"></td>
        </tr>
    </table>

    <!-- Table Rincian Transaksi -->
    <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse; border: 1px solid #dddddd;">
        <thead>
            <tr style="background-color: #111111; color: #ffffff;">
                <th style="text-align: left; font-size: 10pt; font-weight: bold; width: 15%;">No. Invoice</th>
                <th style="text-align: left; font-size: 10pt; font-weight: bold; width: 15%;">Tanggal</th>
                <th style="text-align: left; font-size: 10pt; font-weight: bold; width: 25%;">Pelanggan</th>
                <th style="text-align: center; font-size: 10pt; font-weight: bold; width: 15%;">Status Order</th>
                <th style="text-align: center; font-size: 10pt; font-weight: bold; width: 15%;">Status Bayar</th>
                <th style="text-align: right; font-size: 10pt; font-weight: bold; width: 15%;">Total Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
                <tr style="background-color: {{ $index % 2 === 0 ? '#ffffff' : '#fcfcfc' }};">
                    <td style="font-weight: bold; color: #d90429; font-size: 9.5pt;">{{ $order->invoice_number }}</td>
                    <td style="font-size: 9.5pt;">{{ $order->created_at->format('d M Y H:i') }}</td>
                    <td style="font-size: 9.5pt;">
                        <strong>{{ $order->user->name ?? 'Tamu' }}</strong>
                        <span style="font-size: 8pt; color: #888888;"><br>{{ $order->user->email ?? '' }}</span>
                    </td>
                    <td style="text-align: center; font-size: 9pt;">
                        @php
                            $status = strtolower($order->status);
                            $isCompleted = in_array($status, ['completed', 'delivered', 'shipped']);
                            $isCancelled = in_array($status, ['cancelled', 'failed']);
                            $statusColor = $isCompleted ? '#2ec4b6' : ($isCancelled ? '#d90429' : '#00b4d8');
                        @endphp
                        <span style="color: {{ $statusColor }}; font-weight: bold;">{{ strtoupper($order->status) }}</span>
                    </td>
                    <td style="text-align: center; font-size: 9pt;">
                        @php
                            $rawPayStatus = strtolower($order->payment_status);
                            $displayPayStatus = $rawPayStatus === 'settlement' ? 'paid' : $rawPayStatus;
                            $isPaySuccess = in_array($displayPayStatus, ['paid', 'success']);
                            $isPayFailed = in_array($displayPayStatus, ['failed', 'expire', 'expired', 'deny', 'cancel']);
                            $payColor = $isPaySuccess ? '#2ec4b6' : ($isPayFailed ? '#d90429' : '#ffb703');
                        @endphp
                        <span style="color: {{ $payColor }}; font-weight: bold;">{{ strtoupper($displayPayStatus) }}</span>
                    </td>
                    <td style="text-align: right; font-weight: bold; font-size: 9.5pt;">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #888888; padding: 20px;">Tidak ada data transaksi untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Blank Row -->
    <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
        <tr>
            <td colspan="6" style="height: 25px;"></td>
        </tr>
    </table>

    <!-- Bottom Double Section Headers -->
    <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
        <tr>
            <!-- Left Header -->
            <td colspan="3" style="font-size: 11pt; font-weight: bold; color: #111111; border-bottom: 2px solid #eeeeee; padding-bottom: 5px; text-transform: uppercase;">
                Top 5 Produk Terlaris
            </td>
            <!-- Empty column spacing -->
            <td style="width: 4%;"></td>
            <!-- Right Header -->
            <td colspan="2" style="font-size: 11pt; font-weight: bold; color: #111111; border-bottom: 2px solid #eeeeee; padding-bottom: 5px; text-transform: uppercase;">
                Ringkasan Metode Pembayaran
            </td>
        </tr>
        <tr>
            <td colspan="6" style="height: 10px;"></td>
        </tr>
    </table>

    <!-- Bottom Tables Layout -->
    <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
        <tr>
            <!-- Left Table: Best Sellers -->
            <td colspan="3" style="vertical-align: top; width: 48%;">
                <table border="1" cellpadding="6" cellspacing="0" style="width: 100%; border-collapse: collapse; border: 1px solid #dddddd;">
                    <thead>
                        <tr style="background-color: #111111; color: #ffffff;">
                            <th style="text-align: left; font-size: 9.5pt; font-weight: bold;">Nama Produk</th>
                            <th style="text-align: right; font-size: 9.5pt; font-weight: bold; width: 30%;">Terjual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bestSellers as $product)
                            <tr>
                                <td style="font-weight: bold; font-size: 9pt;">{{ $product->name }}</td>
                                <td style="text-align: right; font-weight: bold; color: #d90429; font-size: 9pt;">{{ $product->total_sold }} item</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" style="text-align: center; color: #888888; font-size: 9pt;">Belum ada produk terjual.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </td>

            <!-- Empty cell spacer -->
            <td style="width: 4%;"></td>

            <!-- Right Table: Payment Summary -->
            <td colspan="2" style="vertical-align: top; width: 48%;">
                <table border="1" cellpadding="6" cellspacing="0" style="width: 100%; border-collapse: collapse; border: 1px solid #dddddd;">
                    <thead>
                        <tr style="background-color: #111111; color: #ffffff;">
                            <th style="text-align: left; font-size: 9.5pt; font-weight: bold;">Tipe Pembayaran</th>
                            <th style="text-align: right; font-size: 9.5pt; font-weight: bold; width: 40%;">Total Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $paymentSummaryQuery = \Illuminate\Support\Facades\DB::table('payments')
                                ->join('orders', 'payments.order_id', '=', 'orders.id')
                                ->select('payments.payment_type', \Illuminate\Support\Facades\DB::raw('SUM(orders.grand_total) as total'))
                                ->whereIn('orders.payment_status', ['paid', 'settlement'])
                                ->whereBetween('orders.created_at', [$startDate, $endDate]);
                            if (isset($status) && $status !== 'all') {
                                $paymentSummaryQuery->where('orders.status', $status);
                            }
                            $paymentSummary = $paymentSummaryQuery->groupBy('payments.payment_type')
                                ->get();
                        @endphp
                        @forelse($paymentSummary as $ps)
                            <tr>
                                <td style="font-weight: bold; text-transform: uppercase; font-size: 9pt;">{{ str_replace('_', ' ', $ps->payment_type) }}</td>
                                <td style="text-align: right; font-weight: bold; font-size: 9pt;">Rp {{ number_format($ps->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" style="text-align: center; color: #888888; font-size: 9pt;">Belum ada ringkasan pembayaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <!-- Footer Space -->
    <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
        <tr>
            <td colspan="6" style="height: 30px;"></td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: center; font-size: 8.5pt; color: #888888; border-top: 1px solid #eeeeee; padding-top: 10px;">
                BigSport Premium E-commerce Admin Command Center &copy; {{ date('Y') }}
            </td>
        </tr>
    </table>

</body>
</html>
