<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatingArrangement extends Model
{
    use HasFactory;

    protected $fillable = ['auditorium_id', 'label', 'seat_type', 'x_position', 'y_position'];

    public function auditorium()
    {
        return $this->belongsTo(Auditorium::class);
    }

    public function ticketOrderItems()
    {
        return $this->hasMany(TicketOrderItem::class);
    }
}
