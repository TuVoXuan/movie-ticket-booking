<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Screening extends Model
{
    use HasFactory;

    protected $fillable = ['film_id', 'auditorium_id', 'screening_time', 'film_translation'];

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
    public function ticketPrices()
    {
        return $this->hasMany(TicketPrice::class);
    }
}
