<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketOrder extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'screening_id', 'total', 'email', 'phone', 'name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function screening()
    {
        return $this->belongsTo(Screening::class);
    }

    public function ticketOrderItems()
    {
        return $this->hasMany(TicketOrderItem::class);
    }
}
