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
            
        return view('customer.pages.order', compact('orders'));
    }


    public function show($id)
    {
        // Pakai helper auth() agar tidak terkena error "Class Auth not found"
        $order = Order::with(['items.sku.product.images', 'shippingDetail', 'payment'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('customer.pages.detail_order', compact('order'));
    }
}