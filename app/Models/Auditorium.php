<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditorium extends Model
{
    use HasFactory;

    public function cinemaBranch()
    {
        return $this->belongsTo(CinemaBranch::class);
    }

    public function seatingArrangements()
    {
        return $this->hasMany(SeatingArrangement::class);
    }

    public function screenings()
    {
        return $this->hasMany(Screening::class);
    }
}
