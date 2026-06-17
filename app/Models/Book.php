<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    
    public function authors()
    {
        return $this->belongsToMany(Author::class, 'book_author');
    }
    
    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'book_genre');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
