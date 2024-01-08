<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['is_liked_by_current_user'];


    public function order(){

        return $this->belongsTo(Order::class);
    }

    public function user(){

        return $this->belongsTo(User::class);
    }

    public function store(){

        return $this->belongsTo(Store::class);
    }

    public function comments(){

        return $this->hasMany(Comment::class);
    }

    public function likes(){

        return $this->hasMany(Like::class);
    }

    public function getIsLikedByCurrentUserAttribute()
    {

        $currentUserId = auth('api')->id();

        return $this->likes()->where('user_id', $currentUserId)->exists();
    }

    public function incrementViews()
    {
        $this->increment('views');
    }
}


