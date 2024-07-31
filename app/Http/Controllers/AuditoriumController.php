<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AuditoriumController extends Controller
{
    public function index()
    {
        return Inertia::render('Cinemas/Auditoria/Index');
    }

    public function create()
    {
        return Inertia::render('Cinemas/Auditoria/CreateAndUpdate');
    }
}
