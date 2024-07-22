<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'biography', 'birthday', 'code'];

    public function films()
    {
        return $this->belongsToMany(Film::class, 'film_artist');
    }
}
