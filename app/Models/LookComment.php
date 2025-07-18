<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LookComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'look_id',
        'user_id',
        'comment',
    ];

    // A comment belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class)->select('id', 'name', 'email', 'profile_image');
    }

    // A comment belongs to a look
    public function look()
    {
        return $this->belongsTo(Look::class);
    }

    public function reply()
    {
        return $this->hasMany(LookCommentsReply::class);
    }
}
