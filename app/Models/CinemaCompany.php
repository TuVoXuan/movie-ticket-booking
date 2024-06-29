<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CinemaCompany extends Model
{
    use HasFactory;

    public function branches()
    {
        return $this->hasMany(CinemaBranch::class);
    }
}
