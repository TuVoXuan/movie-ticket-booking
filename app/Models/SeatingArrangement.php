<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatingArrangement extends Model
{
    use HasFactory;

    public function auditorium()
    {
        return $this->belongsTo(Auditorium::class);
    }

    public function ticketOrderItems()
    {
        return $this->hasMany(TicketOrderItem::class);
    }
}
