<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CinemaBranch extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'region_id', 'cinema_company_id', 'latitude', 'longitude', 'code'];

    public function cinemaCompany()
    {
        return $this->belongsTo(CinemaCompany::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function auditoriums()
    {
        return $this->hasMany(Auditorium::class);
    }
}
