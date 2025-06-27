<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Message;
use App\Events\MessageSent;

class MessageController extends Controller
{
    /**
     * send message
     * @param $request mixed
     * @return json
     */
    public function send(Request $request)
    {
          $validator = Validator::make($request->all(), [
            'to_user_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        if ($validator->fails()) {
            return returnErrorWithData('Validation failed', $validator->errors());
        }

        $data = Message::create([
            'from_user_id' => $request->user()->id,
            'to_user_id' => $request->to_user_id,
            'message' => $request->message,
        ]);
  

        event(new \App\Events\MessageSent($data->toArray()));
          //broadcast(new MessageSent($data->toArray()))->toOthers();

        return response()->json(['status' => 'Message Sent', 'data' => $data]);
    }
    
    /**
     *  message list
     * @param $request mixed
     * @return json
     */
    public function chatList(Request $request)
    {
        $userId = $request->user()->id;

        $messages = Message::where('from_user_id', $userId)
            ->orWhere('to_user_id', $userId)
            ->latest()
            ->get()
            ->groupBy(function ($msg) use ($userId) {
                return $msg->from_user_id == $userId ? $msg->to_user_id : $msg->from_user_id;
            });

        $list = [];

        foreach ($messages as $partnerId => $msgs) {
            $last = $msgs->first();
            $list[] = [
                'user_id' => $partnerId,
                'last_message' => $last->message,
                'sent_at' => $last->created_at->diffForHumans(),
            ];
        }

        return returnSuccess('Messages fetched successfully.',  $list);
    }

    /**
     *  specific message list
     * @param $request mixed
     * @return json
     */
    public function chatWith(Request $request, $userId)
    {
        $authId = $request->user()->id;

        $messages = Message::where(function ($q) use ($authId, $userId) {
            $q->where('from_user_id', $authId)->where('to_user_id', $userId);
        })->orWhere(function ($q) use ($authId, $userId) {
            $q->where('from_user_id', $userId)->where('to_user_id', $authId);
        })->orderBy('created_at', 'asc')->get();

        $formatted = $messages->map(function ($msg) use ($authId) {
            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'sent_by_me' => $msg->from_user_id == $authId,
                'created_at' => $msg->created_at->toDateTimeString()
            ];
        });

        return returnSuccess('Chat loaded successfully.', $formatted);
    }

}

