<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RealTimePromoNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $title;
    public $message;
    protected $userIds;

    public function __construct($title, $message, $userIds) {
        $this->title = $title;
        $this->message = $message;
        $this->userIds = $userIds;
    }

    public function broadcastOn(): array {
        $channels = [];
        foreach ($this->userIds as $userId) {
            $channels[] = new PrivateChannel('user-notifications.' . $userId);
        }
        return $channels;
    }

    public function broadcastAs() {
        return 'notif.new';
    }

    public function broadcastWith(): array {
        return [
            'title' => $this->title,
            'message' => $this->message,
        ];
    }
}
