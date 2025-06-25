<?php

namespace App\Http\Controllers\API;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;


class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        if ($validator->fails()) {
            return returnErrorWithData('Validation failed', $validator->errors());
        }

        $chat = Chat::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message
        ]);

        // 🔥 Notify Socket.IO Server via HTTP POST or Redis
        // Here we assume HTTP POST
        // Http::post('http://localhost:3000/new-message', [
        //     'sender_id' => $chat->sender_id,
        //     'receiver_id' => $chat->receiver_id,
        //     'message' => $chat->message
        // ]);

        return response()->json(['status' => 'Message sent']);
    }

    public function chatHistory($userId)
    {
        $chats = Chat::where(function ($query) use ($userId) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('sender_id', $userId)
                ->where('receiver_id', Auth::id());
        })->orderBy('created_at')->get();

        return response()->json($chats);
    }

    public function uploadMedia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'media' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,avi,doc,pdf|max:20480' // 20MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $path = $request->file('media')->store('chat_media', 's3');
        $url = Storage::disk('s3')->url($path);

        $chat = Chat::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => null,
            'media_url' => $url
        ]);

        // Notify Socket.IO
        // Http::post('http://localhost:3000/new-message', [
        //     'sender_id' => $chat->sender_id,
        //     'receiver_id' => $chat->receiver_id,
        //     'media_url' => $chat->media_url
        // ]);

        return response()->json(['status' => true, 'media_url' => $url]);
    }
}
