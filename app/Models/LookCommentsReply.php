<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LookCommentsReply extends Model
{
    use SoftDeletes;
    protected $table = 'look_comments_reply';

    protected $fillable = [
        'look_comment_id',
        'content',
    ];

    public function comment()
    {
        return $this->belongsTo(LookComment::class, 'look_comment_id');
    }
}
