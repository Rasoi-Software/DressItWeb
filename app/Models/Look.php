<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Look extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'set_goal', 'description', 'location'];

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
