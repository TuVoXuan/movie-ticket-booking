<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Screening extends Model
{
    use HasFactory;

    public function film()
    {
        return $this->belongsTo(Film::class);
    }

    public function auditorium()
    {
        return $this->belongsTo(Auditorium::class);
    }

    public function ticketOrders()
    {
        return $this->hasMany(TicketOrder::class);
    }
}
