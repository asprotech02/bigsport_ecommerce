<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();

        $totalOrders = Order::count();

        $totalCustomers = User::where('role', 'customer')->count();

        $pendingOrders = Order::where('status', 'pending')->count();

        // Count both paid and settlement states
        $totalRevenue = Order::whereIn('payment_status', ['paid', 'settlement'])
            ->sum('grand_total');

        // 1. Recent Orders (5)
        $recentOrders = Order::with(['user', 'payment'])
            ->latest()
            ->take(5)
            ->get();

        // 2. Best Sellers (Top 5)
        $bestSellers = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_skus', 'order_items.product_sku_id', '=', 'product_skus.id')
            ->join('products', 'product_skus.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->whereIn('orders.payment_status', ['paid', 'settlement'])
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // 3. Sales Trend for the last 7 days (continuous)
        $rawSalesTrend = DB::table('orders')
            ->selectRaw('DATE(created_at) as date, SUM(grand_total) as total')
            ->whereIn('payment_status', ['paid', 'settlement'])
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->get();

        $salesTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateStr = now()->subDays($i)->format('Y-m-d');
            $displayStr = now()->subDays($i)->format('d M');
            $dayTotal = $rawSalesTrend->firstWhere('date', $dateStr)->total ?? 0;
            $salesTrend[] = [
                'date' => $displayStr,
                'total' => (float)$dayTotal
            ];
        }

        // 4. Order Status Distribution
        $rawStatusCounts = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statusCounts = [
            'pending' => $rawStatusCounts['pending'] ?? 0,
            'processing' => $rawStatusCounts['processing'] ?? 0,
            'completed' => $rawStatusCounts['completed'] ?? 0,
            'cancelled' => $rawStatusCounts['cancelled'] ?? 0,
        ];

        return view('admin.pages.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'totalCustomers',
            'pendingOrders',
            'totalRevenue',
            'recentOrders',
            'bestSellers',
            'salesTrend',
            'statusCounts'
        ));
    }
}