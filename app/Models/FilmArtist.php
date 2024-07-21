<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilmArtist extends Model
{
    use HasFactory;
    protected $table = 'film_artist';

    protected $fillable = ['film_id', 'artist_id', 'artist_type'];

    public function film()
    {
        return $this->belongsTo(Film::class);
    }

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }
}
