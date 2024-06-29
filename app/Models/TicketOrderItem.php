<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketOrderItem extends Model
{
    use HasFactory;

    public function ticketOrder()
    {
        return $this->belongsTo(TicketOrder::class);
    }

    public function seatingArrangement()
    {
        return $this->belongsTo(SeatingArrangement::class);
    }
}
