<?php

namespace App\Http\Controllers;

use App\Enums\ArtistType;
use App\Helpers\SlugHelper;
use App\Models\File;
use App\Models\Film;
use App\Models\FilmArtist;
use App\Models\FilmGenre;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class FilmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Films/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Films/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $body = $request->all();
            $validated = Validator::make($body, [
                'title' => 'required|string|min:10',
                'release_date' => 'required|date',
                'duration' => 'required|integer|min:0',
                'age_restricted' => 'required|integer|min:0',
                'trailer' => 'required|string',
                'directors' => 'required|array',
                'directors.*' => 'integer|exists:artists,id',
                'producers' => 'required|array',
                'producers.*' => 'integer|exists:artists,id',
                'actors' => 'required|array',
                'actors.*' => 'integer|exists:artists,id',
                'genres' => 'required|array',
                'genres.*' => 'integer|exists:genres,id',
                'thumbnail' => 'required|image',
                'thumbnail_bg' => 'nullable|image',
                'description' => 'required|string'
            ]);

            if ($validated->fails()) {
                return back()->withErrors($validated->errors());
            }

            $thumbnailId = null;
            if ($request->has('thumbnail')) {
                $thumbnailResult = $request->file('thumbnail')->storeOnCloudinary('movie_ticket_booking');
                $thumbnailURL = $thumbnailResult->getSecurePath();
                $thumbnailPublicId = $thumbnailResult->getPublicId();

                $file = File::create([
                    'public_id' => $thumbnailPublicId,
                    'url' => $thumbnailURL
                ]);

                $thumbnailId = $file->id;
            }

            $thumbnailBgId = null;
            if ($request->has('thumbnail_bg')) {
                $thumbnailBgResult = $request->file('thumbnail_bg')->storeOnCloudinary('movie_ticket_booking');
                $thumbnailBgURL = $thumbnailBgResult->getSecurePath();
                $thumbnailBgPublicId = $thumbnailBgResult->getPublicId();

                $file = File::create([
                    'public_id' => $thumbnailBgPublicId,
                    'url' => $thumbnailBgURL
                ]);

                $thumbnailBgId = $file->id;
            }

            $code = SlugHelper::convertToSlug($body['title']);
            $film = Film::create([
                'title' => $body['title'],
                'release_date' => Carbon::parse($body['release_date']),
                'duration' => $body['duration'],
                'age_restricted' => $body['age_restricted'],
                'trailer' => $body['trailer'],
                'thumbnail' => $thumbnailId,
                'thumbnail_bg' => $thumbnailBgId,
                'description' => $body['description'],
                'code' => $code
            ]);

            foreach ($body['directors'] as $id) {
                FilmArtist::create([
                    'film_id' => $film->id,
                    'artist_id' => $id,
                    'artist_type' => ArtistType::Director->value
                ]);
            }

            foreach ($body['producers'] as $id) {
                FilmArtist::create([
                    'film_id' => $film->id,
                    'artist_id' => $id,
                    'artist_type' => ArtistType::Producer->value
                ]);
            }

            foreach ($body['actors'] as $id) {
                FilmArtist::create([
                    'film_id' => $film->id,
                    'artist_id' => $id,
                    'artist_type' => ArtistType::Actor->value
                ]);
            }

            foreach ($body['genres'] as $id) {
                FilmGenre::create([
                    'film_id' => $film->id,
                    'genre_id' => $id
                ]);
            }

            return redirect()->route('films.index')->with('success', 'Create new film successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return back()->with('error', 'An error occurred during create film.');
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
