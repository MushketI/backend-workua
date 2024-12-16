<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index($userId)
    {
        $user = auth('sanctum')->user();

        // Проверка, авторизован ли пользователь
//        if (!$user) {
//            return response()->json(['error' => 'Unauthorized'], 401);
//        }

        $messages = Message::where(function ($query) use ($user, $userId) {
            $query->where('sender_id', $user->id)
                ->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($user, $userId) {
            $query->where('sender_id', $userId)
                ->where('receiver_id', $user->id);
        })->orderBy('created_at')->get();

        return response()->json($messages);
    }

    //Не работает, сообщения не возвращаются в реальном времени
    public function store(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);


        event(new MessageSent($message));

        return response()->json($message);
    }
}
