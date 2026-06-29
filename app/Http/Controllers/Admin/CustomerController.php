<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CustomerController extends Controller
{
    // List all users with pagination
    public function index()
    {
        $customers = User::orderByDesc('created_at')->paginate(20);
        return view('admin.customers.index', compact('customers'));
    }

    // Show detailed view of a single user
    public function show($id)
    {
        $customer = User::with(['orders', 'addresses'])
            ->findOrFail($id);
        return view('admin.customers.show', compact('customer'));
    }

    // Toggle active status (admin only)
    public function toggleStatus(Request $request, $id)
    {
        $customer = User::findOrFail($id);
        $customer->active = !$customer->active;
        $customer->save();
        return Redirect::back()->with('success', 'Status pengguna diperbarui.');
    }

    // Delete user (admin only)
    public function destroy($id)
    {
        if (auth()->id() == $id) {
            return Redirect::back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang digunakan.');
        }

        $customer = User::findOrFail($id);

        // Delete reviews first to prevent Fkey constraint violation
        \App\Models\Review::where('user_id', $customer->id)->delete();

        // Delete user (orders, addresses, notifications, etc. cascade delete)
        $customer->delete();

        return Redirect::route('admin.customers.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
?>
