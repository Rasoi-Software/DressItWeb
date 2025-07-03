<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'location',
        'age',
        'bio',
        'profile_image',
        'nickname',
        'gender',
        'interested_in',
        'dob',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    protected $appends = [
        'count_follower',
        'count_following',
        'count_look',
    ];

    public function getProfileImageAttribute(): string
    {
        if ($this->attributes['profile_image']) {
            return $this->attributes['profile_image'];
        } else {
            if ($this->gender == 'female') {
                return asset('assets/img/placeholder_female.jpg');
            } else {
                return asset('assets/img/placeholder_male.jpg');
            }
        }
    }


    public function getCountFollowerAttribute()
    {
        return $this->followers()->count();
    }

    public function getCountFollowingAttribute()
    {
        return $this->following()->count();
    }

    public function getCountLookAttribute()
    {
        return $this->looks()->count();
    }
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id');
    }

    public function looks()
    {
        return $this->hasMany(Look::class, 'user_id');
    }
    public function likedLooks()
    {
        return $this->belongsToMany(Look::class, 'look_likes');
    }

    public function lookComments()
    {
        return $this->hasMany(LookComment::class);
    }

    // Users this user has blocked
    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'user_id', 'blocked_id');
    }

    // Users who have blocked this user
    public function blockedBy()
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'blocked_id', 'user_id');
    }
}
