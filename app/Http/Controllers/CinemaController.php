<?php

namespace App\Http\Controllers;

use App\Helpers\SlugHelper;
use App\Models\CinemaCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class CinemaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function companies()
    {
        return Inertia::render('Cinemas/Companies');
    }

    public function branches()
    {
        return Inertia::render('Cinemas/Branches');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeCinema(Request $request)
    {
        try {
            $body = $request->all();
            $validated = Validator::make($body, [
                'name' => 'required|min:5',
                'logo' => 'required|image'
            ]);

            $code = SlugHelper::convertToSlug($body['name']);
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('cinemas.companies.index')->with('error', 'An error occurred during create cinema');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
