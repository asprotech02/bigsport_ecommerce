@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-white font-weight-bold">Business Intelligence: Reports</h1>
    </div>

    <!-- Filter Form -->
    <div class="card shadow border-0 mb-4" style="overflow: visible !important;">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-filter me-1"></i> Filter Rentang Waktu</h6>
        </div>
        <div class="card-body" style="overflow: visible !important;">
            <form action="{{ route('admin.reports.index') }}" method="GET" class="row align-items-end g-3" style="overflow: visible !important;">
                <div class="col-md-3">
                    <label class="form-label text-muted small mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small mb-1">Status Order</label>
                    <select name="status" class="form-control" style="background-color: var(--dark-sidebar); color: white; border: 1px solid var(--border-glass);">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>PENDING</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>PROCESSING</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>COMPLETED</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>DELIVERED</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>SHIPPED</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>CANCELLED</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>FAILED</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-center" style="gap: 12px; overflow: visible !important;">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                    
                    <div class="btn-group shadow-sm w-100" style="overflow: visible !important;">
                        <button type="button" class="btn btn-success dropdown-toggle w-100" data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                        <ul class="dropdown-menu border-0 shadow" style="z-index: 1050 !important; background-color: var(--dark-sidebar);">
                            <li>
                                <a class="dropdown-item py-2 text-white" href="{{ route('admin.reports.csv', ['start_date' => request('start_date', $startDate->format('Y-m-d')), 'end_date' => request('end_date', $endDate->format('Y-m-d')), 'status' => request('status', 'all')]) }}">
                                    <i class="fas fa-file-excel text-success me-2"></i> Export Excel
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 text-white" href="{{ route('admin.reports.pdf', ['start_date' => request('start_date', $startDate->format('Y-m-d')), 'end_date' => request('end_date', $endDate->format('Y-m-d')), 'status' => request('status', 'all')]) }}">
                                    <i class="fas fa-file-pdf text-danger me-2"></i> Export PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row mb-4 g-3">
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 py-2" style="border-left: 4px solid var(--primary-neon) !important; background-color: var(--dark-sidebar);">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-uppercase mb-1" style="color: var(--primary-neon); font-size: 0.75rem;">Total Pendapatan (Paid)</div>
                            <div class="h4 mb-0 fw-bold text-white">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-muted opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 py-2" style="border-left: 4px solid #00b4d8 !important; background-color: var(--dark-sidebar);">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1" style="font-size: 0.75rem;">Total Pesanan</div>
                            <div class="h4 mb-0 fw-bold text-white">{{ number_format($totalOrders) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-bag fa-2x text-muted opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 py-2" style="border-left: 4px solid #2ec4b6 !important; background-color: var(--dark-sidebar);">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1" style="font-size: 0.75rem;">Pesanan Selesai</div>
                            <div class="h4 mb-0 fw-bold text-white">{{ number_format($completedOrders) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-muted opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 py-2" style="border-left: 4px solid #d90429 !important; background-color: var(--dark-sidebar);">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1" style="font-size: 0.75rem;">Pesanan Dibatalkan</div>
                            <div class="h4 mb-0 fw-bold text-white">{{ number_format($cancelledOrders) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-muted opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row">
        <!-- Sales Table -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-white"><i class="fas fa-chart-line me-1"></i> Penjualan Harian (Paid)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="ps-4">Tanggal</th>
                                    <th class="text-end pe-4">Total Pendapatan (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salesData as $data)
                                    <tr>
                                        <td class="ps-4 fw-semibold text-white">{{ \Carbon\Carbon::parse($data->date)->format('d M Y') }}</td>
                                        <td class="text-end pe-4 fw-bold text-white">Rp {{ number_format($data->total, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-5 text-muted">Tidak ada transaksi yang selesai pada rentang tanggal ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Best Sellers -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-white"><i class="fas fa-fire me-1"></i> Top 5 Produk Terlaris</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush" style="background-color: transparent;">
                        @forelse($bestSellers as $product)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4" style="background-color: transparent; border-bottom: 1px solid var(--border-glass);">
                                <div>
                                    <h6 class="mb-1 fw-bold text-white">{{ $product->name }}</h6>
                                    <small class="text-muted">Terjual: {{ $product->total_sold }} item</small>
                                </div>
                                <a href="{{ route('admin.products.index', ['search' => $product->name]) }}" class="btn btn-sm btn-outline-primary rounded-circle">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        @empty
                            <li class="list-group-item text-center py-5 text-muted border-0" style="background-color: transparent;">Belum ada produk yang terjual.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
