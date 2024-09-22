<?php

namespace App\Enums;

enum OrderStatus: string
{
  case Pending = 'pending';
  case Cancel = 'cancel';
  case Paid = 'paid';
}
