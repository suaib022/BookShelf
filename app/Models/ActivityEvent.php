<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityEvent extends Model
{
    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
    
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
