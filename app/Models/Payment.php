<?php

// app/Models/Payment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'payment_intent_id',
        'payment_method_id',
        'amount',
        'currency',
        'status',
        'description',
        'response',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
