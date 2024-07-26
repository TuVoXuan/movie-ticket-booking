<?php

namespace App\Http\Controllers;

use App\Enums\ArtistType;
use App\Helpers\SlugHelper;
use App\Models\File;
use App\Models\Film;
use App\Models\FilmArtist;
use App\Models\FilmGenre;
use Carbon\Carbon;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class FilmController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    try {
      $search = $request->query('search');
      $pageSize = $request->query('page_size', 10);
      $sort = $request->query('sort');
      $sort_order = $request->query('sort_order', 'asc');
      $released_from = $request->query('released_from');
      $released_to = $request->query('released_to');

      $films = Film::when($search, function ($query, $search) {
        $query->where('title', 'LIKE', '%' . $search . '%');
      })->when($sort, function ($query, $sort) use ($sort_order) {
        $query->orderBy($sort, $sort_order);
      })->when($released_from && $released_to, function ($query) use ($released_from, $released_to) {
        $query->whereBetween('release_date', [Carbon::parse($released_from), Carbon::parse($released_to)]);
      })->paginate($pageSize);

      return Inertia::render('Films/Index', ['films' => $films]);
    } catch (\Throwable $th) {
      Log::error($th);
      return Inertia::render('Films/Index')->with('error', 'An error occurred during get list films.');
    }
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return Inertia::render('Films/CreateAndUpdate');
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
    try {
      $film = Film::with(['genres', 'directors', 'producers', 'actors', 'thumbnail', 'thumbnailBg'])->find($id);
      if (!$film) {
        return redirect()->route('films.index')->with('error', 'Film not found.');
      }
      return Inertia::render('Films/CreateAndUpdate', ['film' => $film]);
    } catch (\Throwable $th) {
      Log::error($th);
      return back()->with('error', 'An error occurred during get film.');
    }
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    try {
      $film = Film::find($id);
      if (!$film) {
        return back()->with('error', 'Film not found');
      }

      $body = $request->all();
      $validated = Validator::make($body, [
        'title' => 'required|string|min:10',
        'release_date' => 'required|date',
        'duration' => 'required|integer|min:0',
        'age_restricted' => 'required|integer|min:0',
        'trailer' => 'required|string',
        'directors' => 'sometimes|array',
        'directors.*' => 'sometimes|exists:artists,id',
        'producers' => 'sometimes|array',
        'producers.*' => 'sometimes|exists:artists,id',
        'actors' => 'sometimes|array',
        'actors.*' => 'sometimes|exists:artists,id',
        'genres' => 'sometimes|array',
        'genres.*' => 'sometimes|exists:genres,id',
        'thumbnail' => 'sometimes|image',
        'thumbnail_bg' => 'sometimes|image',
        'description' => 'required|string',
        'deleted_directors' => 'sometimes|array',
        'deleted_directors.*' => 'sometimes|exists:artists,id',
        'deleted_producers' => 'sometimes|array',
        'deleted_producers.*' => 'sometimes|exists:artists,id',
        'deleted_actors' => 'sometimes|array',
        'deleted_actors.*' => 'sometimes|exists:artists,id',
        'deleted_genres' => 'sometimes|array',
        'deleted_genres.*' => 'sometimes|exists:artists,id',
      ]);

      if ($validated->fails()) {
        return back()->withErrors($validated->errors());
      }

      $deletionArtists = ['deleted_directors', 'deleted_producers', 'deleted_actors'];

      foreach ($deletionArtists as $deletedKey) {
        if ($request->has($deletedKey)) {
          foreach ($body[$deletedKey] as $delId) {
            FilmArtist::where(['film_id' => $id, 'artist_id' => $delId])->delete();
          }
        }
      }

      if ($request->has('deleted_genres')) {
        foreach ($body['deleted_genres'] as $genre) {
          FilmGenre::where(['film_id' => $id, 'genre_id' => $genre])->delete();
        }
      }

      $additionArtist = [
        'directors' => ArtistType::Director->value,
        'producers' => ArtistType::Producer->value,
        'actors' => ArtistType::Actor->value
      ];

      foreach ($additionArtist as $key => $artistType) {
        if ($request->has($key)) {
          foreach ($body[$key] as $artistId) {
            FilmArtist::create([
              'film_id' => $id,
              'artist_id' => $artistId,
              'artist_type' => $artistType
            ]);
          }
        }
      }

      if ($request->has('genres')) {
        foreach ($body['genres'] as $genreId) {
          FilmGenre::create([
            'film_id' => $id,
            'genre_id' => $genreId
          ]);
        }
      }

      $updateFiles = ['thumbnail', 'thumbnail_bg'];
      foreach ($updateFiles as $fileType) {
        if ($request->has($fileType)) {
          $oldFile = File::find($film[$fileType]);

          $thumbnailResult = $request->file($fileType)->storeOnCloudinary('movie_ticket_booking');
          $thumbnailURL = $thumbnailResult->getSecurePath();
          $thumbnailPublicId = $thumbnailResult->getPublicId();

          $newFile = File::create([
            'public_id' => $thumbnailPublicId,
            'url' => $thumbnailURL
          ]);

          $film->update([
            $fileType => $newFile->id
          ]);

          if ($oldFile) {
            Cloudinary::destroy($oldFile->public_id);
            $oldFile->delete();
          }
        }
      }

      $code = SlugHelper::convertToSlug($body['title']);
      $film->update([
        'title' => $body['title'],
        'release_date' => Carbon::parse($body['release_date']),
        'duration' => $body['duration'],
        'age_restricted' => $body['age_restricted'],
        'trailer' => $body['trailer'],
        'description' => $body['description'],
        'code' => $code
      ]);

      return redirect()->route('films.index')->with('success', 'Update film successfully.');
    } catch (\Throwable $th) {
      Log::error($th);
      return back()->with('error', 'An error occurred during update film.');
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }
}
