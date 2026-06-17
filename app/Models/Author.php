<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    protected $guarded = [];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_author');
    }
}
