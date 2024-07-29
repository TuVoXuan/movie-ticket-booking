<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CinemaCompany extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'logo', 'code'];

    public function branches()
    {
        return $this->hasMany(CinemaBranch::class);
    }

    public function logo()
    {
        return $this->belongsTo(File::class, 'logo');
    }
}
