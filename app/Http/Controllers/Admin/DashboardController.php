<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\Order;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();

        $totalOrders = Order::count();

        $totalCustomers = User::where('role', 'customer')->count();

        $pendingOrders = Order::where('status', 'pending')->count();

        $totalRevenue = Order::where('payment_status', 'paid')
            ->sum('grand_total');

        return view('admin.pages.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'totalCustomers',
            'pendingOrders',
            'totalRevenue'
        ));
    }
}