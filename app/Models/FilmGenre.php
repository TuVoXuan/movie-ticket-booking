<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilmGenre extends Model
{
    use HasFactory;

    protected $table = 'film_genre';

    protected $fillable = ['genre_id', 'film_id'];

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function film()
    {
        return $this->belongsTo(Film::class);
    }
}
