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

    /**
     * Recompute avg_rating and ratings_count from the ratings table and persist.
     * Call this after any rating is saved or deleted.
     */
    public function updateAvgRating(): void
    {
        $agg = $this->ratings()->selectRaw('count(*) as cnt, avg(stars) as avg')->first();
        $this->update([
            'ratings_count' => $agg->cnt ?? 0,
            'avg_rating'    => round($agg->avg ?? 0, 2),
        ]);
    }
}
