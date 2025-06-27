<?php

namespace App\Events;

// use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
// use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;


class MessageSent implements ShouldBroadcast
{
    public $message;

    public function __construct($message)
    {
        $this->message = $message;
         Log::info('MessageSent Event Fired:', $message);
    }

    public function broadcastOn()
    {
        // return new PrivateChannel('chat.' . $this->message['to_user_id']);
          return new Channel('my-channel');
    }

    public function broadcastAs()
    {
        // return 'message.sent';
        return 'my-event';
    }

     public function broadcastWith()
    {
        return [
            'message' => $this->message,
        ];
    }
}
