<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    use HasFactory;

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'film_genre');
    }

    public function artists()
    {
        return $this->belongsToMany(Artist::class, 'film_artist');
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'film_post');
    }

    public function userLike()
    {
        return $this->belongsToMany(User::class, 'film_diaries');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function screenings()
    {
        return $this->hasMany(Screening::class);
    }
}
