<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketOrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_order_id', 'seating_arrangement_id', 'price'];

    public function ticketOrder()
    {
        return $this->belongsTo(TicketOrder::class);
    }

    public function seatingArrangement()
    {
        return $this->belongsTo(SeatingArrangement::class);
    }
}
