<?php

namespace App\Http\Controllers;

use App\Helpers\SlugHelper;
use App\Models\CinemaCompany;
use App\Models\File;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
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
        return Inertia::render('Cinemas/Companies', ['cinemas' => CinemaCompany::with('logo')->get()]);
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

            if ($validated->fails()) {
                return back()->withErrors($validated->errors());
            }

            $logoResult = $request->file('logo')->storeOnCloudinary('movie_ticket_booking');
            $logoURL = $logoResult->getSecurePath();
            $thumbnailPublicId = $logoResult->getPublicId();

            $file = File::create([
                'public_id' => $thumbnailPublicId,
                'url' => $logoURL
            ]);

            $code = SlugHelper::convertToSlug($body['name']);
            CinemaCompany::create([
                'name' => $body['name'],
                'logo' => $file->id,
                'code' => $code
            ]);

            return redirect()->route('cinemas.companies.index')->with('success', 'Create cinema successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('cinemas.companies.index')->with('error', 'An error occurred during create cinema');
        }
    }

    public function updateCinema(Request $request, string $id)
    {
        try {
            $cinemaCompany = CinemaCompany::find($id);
            if (!$cinemaCompany) {
                return back()->with('error', 'Cinema not found.');
            }

            $body = $request->all();
            $validated = Validator::make($body, [
                'name' => 'required|string|min:3',
                'logo' => 'sometimes|image'
            ]);

            if ($validated->failed()) {
                return back()->withErrors($validated->errors());
            }

            if ($request->has('logo')) {
                $oldLogo = File::find($cinemaCompany->logo);

                $logoResult = $request->file('logo')->storeOnCloudinary('movie_ticket_booking');
                $logoURL = $logoResult->getSecurePath();
                $thumbnailPublicId = $logoResult->getPublicId();

                $file = File::create([
                    'public_id' => $thumbnailPublicId,
                    'url' => $logoURL
                ]);
                $cinemaCompany->update([
                    'logo' => $file->id
                ]);

                if ($oldLogo) {
                    Cloudinary::destroy($oldLogo->public_id);
                    $oldLogo->delete();
                }
            }
            $code = SlugHelper::convertToSlug($body['name']);
            $cinemaCompany->update([
                'name' => $body['name'],
                'code' => $code
            ]);

            return redirect()->route('cinemas.companies.index')->with('success', 'Update cinema successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('cinemas.companies.index')->with('error', 'An error occurred during update cinema');
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
