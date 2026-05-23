<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = UserNotification::with('user')->orderByDesc('created_at')->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        $users = User::where('role', 'customer')->orderBy('name')->get();
        return view('admin.notifications.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'message' => 'required|string',
            'type'    => 'required|string',
            'user_id' => 'required' // "all" or numeric ID
        ]);

        if ($request->user_id === 'all') {
            $users = User::where('role', 'customer')->get();
            $notificationsData = [];
            foreach ($users as $user) {
                $notificationsData[] = [
                    'user_id'    => $user->id,
                    'title'      => $request->title,
                    'message'    => $request->message,
                    'type'       => $request->type,
                    'is_read'    => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            UserNotification::insert($notificationsData);
        } else {
            UserNotification::create([
                'user_id' => $request->user_id,
                'title'   => $request->title,
                'message' => $request->message,
                'type'    => $request->type,
                'is_read' => false,
            ]);
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notifikasi berhasil dikirim.');
    }

    public function destroy($id)
    {
        UserNotification::findOrFail($id)->delete();
        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notifikasi berhasil dihapus dari riwayat.');
    }
}
