<?php

namespace App\Enums;

enum SeatType: string
{
    case Unset = 'unset';
    case Aisle = 'aisle';
    case Normal = 'seat_normal';
    case VIP = 'seat_vip';
}
