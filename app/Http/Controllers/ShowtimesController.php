<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ShowtimesController extends Controller
{
    public function index()
    {
        return Inertia::render('Cinemas/Showtimes/Index');
    }

    public function create()
    {
        return Inertia::render('Cinemas/Showtimes/CreateAndUpdate');
    }
}
