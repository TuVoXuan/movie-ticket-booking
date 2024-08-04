<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditorium extends Model
{
    use HasFactory;

    protected $fillable = ['cinema_branch_id', 'name', 'capacity', 'seat_direction', 'code', 'rows', 'columns'];

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
