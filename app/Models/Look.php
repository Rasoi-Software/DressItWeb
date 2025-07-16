<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Look extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'set_goal',
        'description',
        'location',
        'device_id',
        'status'
    ];
    protected $appends = [
        'count_likes',
        'count_comments',
        'is_like'
    ];

    public function getCountLikesAttribute()
    {
        return $this->likes()->count();
    }

    public function getCountCommentsAttribute()
    {
        return $this->comments()->count();
    }
    public function getIsLikeAttribute()
    {
        $authUser = auth()->user();

        if (!$authUser) {
            return false;
        }

        return $this->likes()->where('user_id', $authUser->id)->exists();
    }


    public function user()
    {
        return $this->belongsTo(User::class)->select('id', 'name', 'email', 'profile_image');
    }
    public function media()
    {
        return $this->hasMany(LookMedia::class);
    }
    public function likes()
    {
        return $this->belongsToMany(User::class, 'look_likes');
    }

    public function comments()
    {
        return $this->hasMany(LookComment::class);
    }
}
