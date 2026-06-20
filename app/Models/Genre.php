<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $guarded = [];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_genre');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_favorite_genres', 'genre_id', 'user_id');
    }
}
