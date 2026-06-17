<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function comments()
    {
        return $this->hasMany(ReviewComment::class);
    }

    public function likes()
    {
        return $this->hasMany(ReviewLike::class);
    }
}
