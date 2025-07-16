<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Events\ChatMessageEvent;


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
            'message' => 'required|string',
            'attachments'     => 'nullable|array|max:5',
            'attachments.*'   => 'file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,wmv',
        ]);

        if ($validator->fails()) {
            return returnErrorWithData('Validation failed', $validator->errors());
        }

        if ($request->hasFile('attachments') && count($request->file('attachments')) > 5) {
            return returnError('You can only upload up to 5 images.');
        }

        // Calculate total size
        $totalSize = array_sum(array_map(function ($file) {
            return $file->getSize();
        }, $request->file('attachments') ?? []));

        if ($totalSize > 100 * 1024 * 1024) { // 100MB
            return returnError('Total image size exceeds 100MB.');
        }
        

        $data = Message::create([
            'from_user_id' => $request->user()->id,
            'to_user_id' => $request->to_user_id,
            'message' => $request->message,
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store("chat/attachments", 's3');
                \Storage::disk('s3')->setVisibility($path, 'public');
                //$url = \Storage::disk('s3')->url($path);

                MessageAttachment::create([
                    'message_id' => $data->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }
  

        event(new ChatMessageEvent($data->toArray()));
          //broadcast(new ChatMessageEvent($data->toArray()))->toOthers();

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

        $messages = Message::with(['attachments','sender', 'receiver'])
             ->where('from_user_id', $userId)
            ->orWhere('to_user_id', $userId)
            ->latest()
            ->get()
            ->groupBy(function ($msg) use ($userId) {
                return $msg->from_user_id == $userId ? $msg->to_user_id : $msg->from_user_id;
            });

        $list = [];

        foreach ($messages as $partnerId => $msgs) {
            $last = $msgs->first();
        
            // Determine the partner user object
            $partner = $last->from_user_id == $userId ? $last->receiver : $last->sender;
        
            $list[] = [
                'user_id' => $partnerId,
                'name' => $partner->name,
                'profile_image' => $partner->profile_image, // Adjust to your DB column
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

        $messages = Message::with('attachments') 
            ->where(function ($q) use ($authId, $userId) {
                $q->where('from_user_id', $authId)
                ->where('to_user_id', $userId);
            })
            ->orWhere(function ($q) use ($authId, $userId) {
                $q->where('from_user_id', $userId)
                ->where('to_user_id', $authId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $formatted = $messages->map(function ($msg) use ($authId) {
            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'sent_by_me' => $msg->from_user_id == $authId,
                'created_at' => $msg->created_at->toDateTimeString(),
                'attachments' => $msg->attachments->map(function ($att) {
                    return [
                        'file_path' => $att->file_path,
                        'file_name' => $att->file_name,
                        'mime_type' => $att->mime_type,
                        'size' => $att->size,
                    ];
                }),
            ];
        });

        return returnSuccess('Chat loaded successfully.', $formatted);
    }

}

