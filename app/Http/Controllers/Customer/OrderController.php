<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        // Tambahkan 'items.sku.product.images' agar gambar bisa dipanggil di View
        $orders = Order::with(['items.sku.product.images'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
            
        return view('pages.customer.order', compact('orders'));
    }
}