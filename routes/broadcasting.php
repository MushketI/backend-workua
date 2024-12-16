<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('private-chat.{receiverId}', function ($user, $receiverId) {

    return true;
//    return (int) $user->id === (int) $receiverId;
});
