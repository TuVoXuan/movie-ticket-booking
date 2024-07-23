<?php

namespace App\Models;

use App\Enums\ArtistType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'release_date',
        'duration',
        'age_restricted',
        'trailer',
        'thumbnail',
        'thumbnail_bg',
        'description',
        'code'
    ];

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'film_genre');
    }

    // public function artists()
    // {
    //     return $this->belongsToMany(Artist::class, 'film_artist');
    // }

    public function directors()
    {
        return $this->belongsToMany(Artist::class, 'film_artist')
            ->where('artist_type', ArtistType::Director->value);
    }
    public function producers()
    {
        return $this->belongsToMany(Artist::class, 'film_artist')
            ->where('artist_type', ArtistType::Producer->value);
    }

    public function Actors()
    {
        return $this->belongsToMany(Artist::class, 'film_artist')
            ->where('artist_type', ArtistType::Actor->value);
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

    public function thumbnail()
    {
        return $this->belongsTo(File::class, 'thumbnail');
    }

    public function thumbnailBg()
    {
        return $this->belongsTo(File::class, 'thumbnail_bg');
    }
}
