<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(30)->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();
        $status = $request->status;

        // Base query for orders
        $orderQuery = Order::whereBetween('created_at', [$startDate, $endDate]);
        if ($status && $status !== 'all') {
            $orderQuery->where('status', $status);
        }

        // Stats
        $totalRevenue = (clone $orderQuery)->whereIn('payment_status', ['paid', 'settlement'])->sum('grand_total');
        $totalOrders = (clone $orderQuery)->count();
        $completedOrders = (clone $orderQuery)->where('status', 'completed')->count();
        $cancelledOrders = (clone $orderQuery)->where('status', 'cancelled')->count();

        // Grafik Penjualan Harian
        $salesDataQuery = DB::table('orders')
            ->selectRaw('DATE(created_at) as date, SUM(grand_total) as total')
            ->whereIn('payment_status', ['paid', 'settlement'])
            ->whereBetween('created_at', [$startDate, $endDate]);
        if ($status && $status !== 'all') {
            $salesDataQuery->where('status', $status);
        }
        $salesData = $salesDataQuery->groupBy('date')
            ->orderBy('date')
            ->get();

        // Best Sellers (Produk Terlaris)
        $bestSellersQuery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_skus', 'order_items.product_sku_id', '=', 'product_skus.id')
            ->join('products', 'product_skus.product_id', '=', 'products.id')
            ->select('products.name', 'products.id as product_id', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->whereIn('orders.payment_status', ['paid', 'settlement'])
            ->whereBetween('orders.created_at', [$startDate, $endDate]);
        if ($status && $status !== 'all') {
            $bestSellersQuery->where('orders.status', $status);
        }
        $bestSellers = $bestSellersQuery->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return view('admin.reports.index', compact(
            'startDate', 'endDate', 'totalRevenue', 'totalOrders', 'completedOrders', 'cancelledOrders', 'salesData', 'bestSellers'
        ));
    }

    public function exportCsv(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(30)->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();
        $status = $request->status;

        $orderQuery = Order::with(['user', 'items.sku.product', 'payment'])
            ->whereBetween('created_at', [$startDate, $endDate]);
        if ($status && $status !== 'all') {
            $orderQuery->where('status', $status);
        }
        $orders = $orderQuery->orderBy('created_at')->get();

        $statsQuery = Order::whereBetween('created_at', [$startDate, $endDate]);
        if ($status && $status !== 'all') {
            $statsQuery->where('status', $status);
        }
        $totalRevenue = (clone $statsQuery)->whereIn('payment_status', ['paid', 'settlement'])->sum('grand_total');
        $totalOrders = (clone $statsQuery)->count();
        $completedOrders = (clone $statsQuery)->where('status', 'completed')->count();
        $cancelledOrders = (clone $statsQuery)->where('status', 'cancelled')->count();

        // Best Sellers (Produk Terlaris)
        $bestSellersQuery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_skus', 'order_items.product_sku_id', '=', 'product_skus.id')
            ->join('products', 'product_skus.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->whereIn('orders.payment_status', ['paid', 'settlement'])
            ->whereBetween('orders.created_at', [$startDate, $endDate]);
        if ($status && $status !== 'all') {
            $bestSellersQuery->where('orders.status', $status);
        }
        $bestSellers = $bestSellersQuery->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $filename = 'laporan_penjualan_' . $startDate->format('Ymd') . '_to_' . $endDate->format('Ymd') . '.xls';
        
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'max-age=0',
        ];

        return response()->view('admin.reports.excel_template', compact(
            'orders', 'startDate', 'endDate', 'totalRevenue', 'totalOrders', 'completedOrders', 'cancelledOrders', 'bestSellers', 'status'
        ))->withHeaders($headers);
    }
    
    public function exportPdf(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(30)->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();
        $status = $request->status;

        $orderQuery = Order::with(['user', 'items.sku.product', 'payment'])
            ->whereBetween('created_at', [$startDate, $endDate]);
        if ($status && $status !== 'all') {
            $orderQuery->where('status', $status);
        }
        $orders = $orderQuery->orderBy('created_at')->get();

        $statsQuery = Order::whereBetween('created_at', [$startDate, $endDate]);
        if ($status && $status !== 'all') {
            $statsQuery->where('status', $status);
        }
        $totalRevenue = (clone $statsQuery)->whereIn('payment_status', ['paid', 'settlement'])->sum('grand_total');
        $totalOrders = (clone $statsQuery)->count();
        $completedOrders = (clone $statsQuery)->where('status', 'completed')->count();
        $cancelledOrders = (clone $statsQuery)->where('status', 'cancelled')->count();

        // Best Sellers (Produk Terlaris)
        $bestSellersQuery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_skus', 'order_items.product_sku_id', '=', 'product_skus.id')
            ->join('products', 'product_skus.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->whereIn('orders.payment_status', ['paid', 'settlement'])
            ->whereBetween('orders.created_at', [$startDate, $endDate]);
        if ($status && $status !== 'all') {
            $bestSellersQuery->where('orders.status', $status);
        }
        $bestSellers = $bestSellersQuery->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Convert Logo to base64
        $logoPath = public_path('assets/logo.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoBase64 = 'data:image/png;base64,' . $logoData;
        }

        $pdf = Pdf::loadView('admin.reports.pdf_template', compact(
            'orders', 'startDate', 'endDate', 'totalRevenue', 'totalOrders', 'completedOrders', 'cancelledOrders', 'bestSellers', 'logoBase64', 'status'
        ));

        return $pdf->download('sales_report_' . $startDate->format('Ymd') . '_to_' . $endDate->format('Ymd') . '.pdf');
    }
}
