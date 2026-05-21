<?php

namespace App\Events;

use App\Models\UserNotification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// 🌟 WAJIB implements ShouldBroadcastNow agar langsung dikirim tanpa antrean (queue)
class RealTimeNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct($notification) {
        $this->notification = $notification;
    }

    public function broadcastOn(): array {
        return [new PrivateChannel('user-notifications.' . $this->notification->user_id)];
    }

    public function broadcastAs() {
        return 'notif.new'; // Nama event di Javascript nanti
    }

    public function broadcastWith(): array {
        return [
            'title' => $this->notification->title,
            'message' => $this->notification->message,
        ];
    }
}