<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CustomerController extends Controller
{
    // List all customers with pagination
    public function index()
    {
        $customers = User::where('role', 'customer')
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('admin.customers.index', compact('customers'));
    }

    // Show detailed view of a single customer
    public function show($id)
    {
        $customer = User::with(['orders', 'addresses'])
            ->where('role', 'customer')
            ->findOrFail($id);
        return view('admin.customers.show', compact('customer'));
    }

    // Toggle active status (admin only)
    public function toggleStatus(Request $request, $id)
    {
        $customer = User::findOrFail($id);
        $customer->active = !$customer->active;
        $customer->save();
        return Redirect::back()->with('success', 'Status pelanggan diperbarui.');
    }
}
?>
