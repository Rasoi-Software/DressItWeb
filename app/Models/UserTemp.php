<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTemp extends Model
{
    protected $table = 'user_temp';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'otp',
        'otp_expires_at',
    ];
}
