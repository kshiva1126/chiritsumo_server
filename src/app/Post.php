<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title', 'content', 'writer_id', 'created_at', 'updated_at',
    ];

    public function writer()
    {
        return $this->belongsTo(User::class);
    }

    public function favorite_users()
    {
        return $this->belongsToMany(User::class);
    }
}
