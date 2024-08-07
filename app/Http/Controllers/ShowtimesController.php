<?php

namespace App\Http\Controllers;

use App\Enums\FilmTranslation;
use App\Models\Screening;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

    public function store(Request $request, string $branch)
    {
        try {
            $body = $request->all();
            $validated = Validator::make($body, [
                'film' => 'required|numeric|exists:films,id',
                'auditorium' => 'required|numeric|exists:auditoria,id',
                'screening_time' => 'required|date',
                'film_translation' => ['required', Rule::enum(FilmTranslation::class)]
            ]);

            if ($validated->fails()) {
                return back()->withErrors($validated->errors());
            }

            Screening::create([
                'film_id' => $body['film'],
                'auditorium_id' => $body['auditorium'],
                'screening_time' => Carbon::parse($body['screening_time']),
                'film_translation' => $body['film_translation']
            ]);

            return redirect()->route('cinemas.branches.showtimes.index', ['branch' => $branch])->with('success', 'Create new screening successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return back()->with('error', 'And error occurred during create screening.');
        }
    }
}
