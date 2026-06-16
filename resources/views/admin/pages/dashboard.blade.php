@extends('admin.layouts.app')

@section('content')
<!-- Futuristic Admin Cockpit Title -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-white font-weight-bold tracking-tight">Dashboard Admin</h1>
        <p class="text-xs text-muted mb-0">Selamat datang kembali! Berikut adalah ringkasan operasional Bagindo Jaya saat ini.</p>
    </div>
    <div class="text-xs text-muted">
        <i class="fas fa-circle text-success mr-1 animate-pulse"></i> Sistem Online &bull; {{ now()->format('d M Y, H:i') }} WIB
    </div>
</div>

<!-- 1. Neon Hero Stats Grid -->
<div class="row">
    <!-- Total Revenue Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 h-100 position-relative overflow-hidden glow-card glow-card-red" style="background: var(--dark-card);">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-uppercase tracking-wider text-muted mb-2">Total Pendapatan</div>
                        <div class="h3 mb-2 font-weight-extrabold text-white">
                            Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                        </div>
                        <div class="progress progress-xs bg-dark-eval mb-0" style="height: 4px; border-radius: 2px;">
                            <div class="progress-bar bg-gradient-danger" role="progressbar" style="width: 85%;" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="col-auto pl-0">
                        <div class="icon-circle-glow icon-glow-red">
                            <i class="fas fa-wallet fa-lg text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Orders Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 h-100 position-relative overflow-hidden glow-card glow-card-blue" style="background: var(--dark-card);">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-uppercase tracking-wider text-muted mb-2">Total Transaksi</div>
                        <div class="h3 mb-2 font-weight-extrabold text-white">
                            {{ number_format($totalOrders) }} <span class="text-xs text-muted font-weight-normal">orders</span>
                        </div>
                        <div class="progress progress-xs bg-dark-eval mb-0" style="height: 4px; border-radius: 2px;">
                            <div class="progress-bar bg-gradient-info" role="progressbar" style="width: 70%;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="col-auto pl-0">
                        <div class="icon-circle-glow icon-glow-blue">
                            <i class="fas fa-shopping-bag fa-lg text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Customers Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 h-100 position-relative overflow-hidden glow-card glow-card-violet" style="background: var(--dark-card);">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-uppercase tracking-wider text-muted mb-2">Total Pelanggan</div>
                        <div class="h3 mb-2 font-weight-extrabold text-white">
                            {{ number_format($totalCustomers) }} <span class="text-xs text-muted font-weight-normal">users</span>
                        </div>
                        <div class="progress progress-xs bg-dark-eval mb-0" style="height: 4px; border-radius: 2px;">
                            <div class="progress-bar bg-gradient-violet" role="progressbar" style="width: 60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="col-auto pl-0">
                        <div class="icon-circle-glow icon-glow-violet">
                            <i class="fas fa-users fa-lg text-violet"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Orders Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 h-100 position-relative overflow-hidden glow-card glow-card-gold" style="background: var(--dark-card);">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs font-weight-bold text-uppercase tracking-wider text-muted mb-2">Pesanan Pending</div>
                        <div class="h3 mb-2 font-weight-extrabold text-white">
                            {{ number_format($pendingOrders) }} <span class="text-xs text-muted font-weight-normal">aktif</span>
                        </div>
                        <div class="progress progress-xs bg-dark-eval mb-0" style="height: 4px; border-radius: 2px;">
                            <div class="progress-bar bg-gradient-gold" role="progressbar" style="width: 45%;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="col-auto pl-0">
                        <div class="icon-circle-glow icon-glow-gold">
                            <i class="fas fa-hourglass-half fa-lg text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2. Charts Section -->
<div class="row">
    <!-- Sales Trend Line Chart (2/3 width) -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header d-flex flex-row align-items-center justify-content-between border-0">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-chart-line text-danger mr-1"></i> Tren Grafik Penjualan (7 Hari Terakhir)</h6>
                <span class="badge bg-danger text-white text-xs px-2 py-1">Omzet Real-time</span>
            </div>
            <div class="card-body">
                <div class="chart-area" style="height: 320px;">
                    <canvas id="salesTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Doughnut Chart (1/3 width) -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header d-flex flex-row align-items-center justify-content-between border-0">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-chart-pie text-danger mr-1"></i> Status Operasional Pesanan</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-2 pb-2" style="height: 240px; position: relative;">
                    <canvas id="orderStatusChart"></canvas>
                </div>
                <div class="mt-4 text-center small text-white-50">
                    <span class="mr-2"><i class="fas fa-circle text-warning mr-1"></i> Pending</span>
                    <span class="mr-2"><i class="fas fa-circle text-info mr-1"></i> Processing</span>
                    <span class="mr-2"><i class="fas fa-circle text-success mr-1"></i> Completed</span>
                    <span><i class="fas fa-circle text-danger mr-1"></i> Cancelled</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. Tables & Details Grid -->
<div class="row">
    <!-- Left Column: Recent Transactions -->
    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header d-flex flex-row align-items-center justify-content-between border-0">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-history text-danger mr-1"></i> 5 Transaksi Terbaru</h6>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-xs btn-outline-primary py-1 px-2" style="font-size: 0.7rem; border-radius: 6px;">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-align-middle mb-0">
                        <thead>
                            <tr class="table-light text-uppercase fs-7" style="font-size: 0.72rem;">
                                <th class="ps-4">No. Invoice</th>
                                <th>Pelanggan</th>
                                <th>Status Order</th>
                                <th>Status Bayar</th>
                                <th class="text-right pe-4">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                @php
                                    $rawPay = strtolower($order->payment_status);
                                    $displayPay = $rawPay === 'settlement' ? 'paid' : $rawPay;
                                    $payColor = in_array($displayPay, ['paid', 'success']) ? 'success' : (in_array($displayPay, ['failed', 'expire', 'expired', 'cancel', 'deny']) ? 'danger' : 'warning');
                                    
                                    $rawOrder = strtolower($order->status);
                                    $orderColor = $rawOrder === 'completed' ? 'success' : ($rawOrder === 'cancelled' ? 'danger' : 'info');
                                @endphp
                                <tr>
                                    <td class="ps-4 font-weight-bold">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-danger hover-underline">
                                            {{ $order->invoice_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="font-weight-semibold text-white">{{ $order->user->name ?? 'Tamu' }}</div>
                                        <div class="text-xs text-muted">{{ $order->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $orderColor }} text-white text-uppercase" style="font-size: 0.65rem;">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $payColor }} text-white text-uppercase" style="font-size: 0.65rem;">
                                            {{ $displayPay }}
                                        </span>
                                    </td>
                                    <td class="text-right pe-4 font-weight-bold text-white">
                                        Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi saat ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Best Selling Products -->
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header border-0">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-fire text-danger mr-1"></i> Produk Terlaris</h6>
            </div>
            <div class="card-body">
                @php
                    $maxSold = $bestSellers->max('total_sold') ?: 1;
                @endphp
                @forelse($bestSellers as $product)
                    @php
                        $percentage = min(100, round(($product->total_sold / $maxSold) * 100));
                        $barColors = ['bg-danger', 'bg-info', 'bg-warning', 'bg-success', 'bg-secondary'];
                        $currentColor = $barColors[$loop->index % count($barColors)];
                    @endphp
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="font-weight-semibold text-white text-truncate mr-2" style="max-width: 70%; font-size: 0.82rem;">
                                {{ $product->name }}
                            </span>
                            <span class="badge bg-dark-eval text-white font-weight-bold" style="font-size: 0.72rem;">
                                {{ $product->total_sold }} item
                            </span>
                        </div>
                        <div class="progress progress-sm" style="height: 6px; border-radius: 3px; background: rgba(255,255,255,0.04);">
                            <div class="progress-bar {{ $currentColor }}" role="progressbar" style="width: {{ $percentage }}%; border-radius: 3px;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <div class="mb-3"><i class="fas fa-box-open fa-3x text-muted opacity-3"></i></div>
                        <p class="mb-0">Belum ada data produk terlaris.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Custom Glassmorphic Styles and Animations -->
@push('styles')
<style>
    /* Neon Glowing Stats Card styling */
    .glow-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(255, 255, 255, 0.05) !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
    }
    .glow-card:hover {
        transform: translateY(-5px);
        border-color: rgba(255, 255, 255, 0.12) !important;
    }
    
    .glow-card-red:hover {
        box-shadow: 0 8px 25px rgba(229, 9, 20, 0.2) !important;
    }
    .glow-card-blue:hover {
        box-shadow: 0 8px 25px rgba(9, 132, 227, 0.2) !important;
    }
    .glow-card-violet:hover {
        box-shadow: 0 8px 25px rgba(108, 92, 231, 0.2) !important;
    }
    .glow-card-gold:hover {
        box-shadow: 0 8px 25px rgba(255, 159, 67, 0.2) !important;
    }
    
    /* Gilded Icons styling */
    .icon-circle-glow {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }
    
    .icon-glow-red { background: rgba(229, 9, 20, 0.08); border-color: rgba(229, 9, 20, 0.15); }
    .icon-glow-blue { background: rgba(9, 132, 227, 0.08); border-color: rgba(9, 132, 227, 0.15); }
    .icon-glow-violet { background: rgba(108, 92, 231, 0.08); border-color: rgba(108, 92, 231, 0.15); }
    .icon-glow-gold { background: rgba(255, 159, 67, 0.08); border-color: rgba(255, 159, 67, 0.15); }
    
    .text-violet { color: #a55eea !important; }
    .bg-gradient-violet { background: linear-gradient(135deg, #8854d0, #a55eea) !important; }
    .bg-gradient-gold { background: linear-gradient(135deg, #fa8231, #f7b731) !important; }
    .bg-gradient-danger { background: linear-gradient(135deg, #eb3b5a, #e50914) !important; }
    .bg-gradient-info { background: linear-gradient(135deg, #2d98da, #0a3d62) !important; }
    
    .bg-dark-eval { background: rgba(0, 0, 0, 0.3) !important; }
    .hover-underline:hover { text-decoration: underline !important; }
    
    .animate-pulse {
        animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse-ring {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: .4; transform: scale(0.95); }
    }
    
    .table-align-middle td, .table-align-middle th {
        vertical-align: middle !important;
    }
</style>
@endpush

<!-- Real-time Chart.js Integration script -->
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Sales Trend Line Chart Configuration
    const salesCtx = document.getElementById('salesTrendChart').getContext('2d');
    
    const trendDates = {!! json_encode(array_column($salesTrend, 'date')) !!};
    const trendTotals = {!! json_encode(array_column($salesTrend, 'total')) !!};
    
    // Create soft gradient for glowing line
    const salesGrad = salesCtx.createLinearGradient(0, 0, 0, 300);
    salesGrad.addColorStop(0, 'rgba(229, 9, 20, 0.35)');
    salesGrad.addColorStop(1, 'rgba(229, 9, 20, 0.01)');

    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: trendDates,
            datasets: [{
                label: 'Omzet Penjualan (Rp)',
                data: trendTotals,
                borderColor: '#e50914',
                borderWidth: 3,
                backgroundColor: salesGrad,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#e50914',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#18181f',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: 'rgba(255,255,255,0.08)',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            return ' Rp ' + context.raw.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    grid: { color: 'rgba(255,255,255,0.04)' },
                    ticks: {
                        color: '#8e8e9f',
                        font: { size: 10 },
                        callback: function(value) {
                            if (value >= 1000000) return (value / 1000000) + ' Jt';
                            if (value >= 1000) return (value / 1000) + ' Rb';
                            return value;
                        }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#8e8e9f', font: { size: 10 } }
                }
            }
        }
    });

    // 2. Order Status Doughnut Chart Configuration
    const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
    const statusCounts = {!! json_encode($statusCounts) !!};

    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Processing', 'Completed', 'Cancelled'],
            datasets: [{
                data: [
                    statusCounts.pending,
                    statusCounts.processing,
                    statusCounts.completed,
                    statusCounts.cancelled
                ],
                backgroundColor: ['#f7b731', '#2d98da', '#2ec4b6', '#eb3b5a'],
                borderColor: '#18181f',
                borderWidth: 3,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#18181f',
                    borderColor: 'rgba(255,255,255,0.08)',
                    borderWidth: 1,
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff'
                }
            }
        }
    });
});
</script>
@endpush
@endsection