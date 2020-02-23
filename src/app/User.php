<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Post;
use App\Favorite;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'image_path', 'description',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class, 'writer_id');
    }

    public function favorite_posts()
    {
        return $this
            ->belongsToMany(Post::class, 'favorites', 'user_id', 'post_id')
            ->withPivot(['created_at', 'updated_at', 'id'])
            ->orderBy('pivot_updated_at', 'desc')
            ->orderBy('pivot_created_at', 'desc')
            ->orderBy('pivot_id', 'desc');
    }

    public function followees()
    {
        return $this ->belongsToMany(User::class, 'followings', 'user_id', 'following_user_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followings', 'following_user_id', 'user_id');
    }

}
