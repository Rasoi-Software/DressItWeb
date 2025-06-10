<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LookMedia extends Model
{
    use HasFactory;

    protected $fillable = ['look_id', 'media_path', 'media_type'];

    public function look()
    {
        return $this->belongsTo(Look::class);
    }
}

