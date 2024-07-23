<?php

use App\Http\Controllers\API\ArtistController;
use App\Http\Controllers\API\FilmController;
use App\Http\Controllers\API\GenreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::get('artists', [ArtistController::class, 'getList'])->name('apiArtists.index');
    Route::get('genres', [GenreController::class, 'getAll'])->name('apiGenres.index');
    Route::get('films/{id}', [FilmController::class, 'getFilmDetails'])->name('apiFilms.show');
});
