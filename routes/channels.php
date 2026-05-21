<?php

use Illuminate\Support\Facades\Broadcast;

// Hanya user yang sedang login yang boleh mendengarkan notifikasinya sendiri
Broadcast::channel('user-notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
