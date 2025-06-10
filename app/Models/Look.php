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
        return $this->belongsTo(User::class);
    }
    public function media()
    {
        return $this->hasMany(LookMedia::class);
    }
}
