<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $guarded = [];

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
    
    public function shelves()
    {
        return $this->hasMany(Shelf::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function activityEvents()
    {
        return $this->hasMany(ActivityEvent::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followee_id', 'follower_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followee_id');
    }

    public function favoriteGenres()
    {
        return $this->belongsToMany(Genre::class, 'user_favorite_genres', 'user_id', 'genre_id');
    }

    public function isActiveAdmin()
    {
        return $this->role === 'admin' && $this->active_mode === 'admin';
    }

    public function isActiveUser()
    {
        // If they are a regular user, or if they are an admin currently in user mode
        return $this->role === 'user' || $this->active_mode === 'user';
    }
}
