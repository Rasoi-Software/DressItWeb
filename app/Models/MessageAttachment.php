<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MessageAttachment extends Model
{
    protected $fillable = [
        'message_id', 'file_path', 'file_name', 'mime_type', 'size',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
    public function getFilePathAttribute()
    {
    // return asset(Storage::url($this->attributes['media_path']));
      return Storage::disk('s3')->url($this->attributes['file_path']);
    }
}
