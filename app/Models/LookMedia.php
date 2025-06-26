<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
class LookMedia extends Model
{
    use HasFactory;

    protected $fillable = ['look_id', 'media_path', 'media_type'];
    protected $appends = ['media_url'];
    public function look()
    {
        return $this->belongsTo(Look::class);
    }
    public function getMediaUrlAttribute()
    {
    return asset(Storage::url($this->attributes['media_path']));
    }
}
