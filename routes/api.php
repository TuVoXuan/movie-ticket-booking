<?php

use App\Http\Controllers\API\ArtistController;
use App\Http\Controllers\API\AuditoriumController;
use App\Http\Controllers\API\CinemaController;
use App\Http\Controllers\API\FilmController;
use App\Http\Controllers\API\GenreController;
use App\Http\Controllers\API\RegionController;
use App\Http\Controllers\API\ShowtimesController;
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

    Route::get('regions', [RegionController::class, 'getList'])->name('apiRegions.index');
    Route::get('regions/{code}', [RegionController::class, 'findByCode'])->name('apiRegions.findByCode');

    Route::get('cinemas/companies', [CinemaController::class, 'getAllCompany'])->name('apiCinemas.companies.index');
    Route::get('cinemas/companies/{code}', [CinemaController::class, 'findByCode'])->name('apiCinemas.companies.findByCode');
    Route::get('cinemas/branch/{branch}/auditoria', [AuditoriumController::class, 'getAllAuditoria'])->name('apiCinemas.branches.auditoria.getAll');
    Route::get('cinemas/branch/{branch}/auditoria/{auditorium}', [AuditoriumController::class, 'getByCode'])->name('apiCinemas.branches.auditoria.getByCode');
    Route::get('cinemas/branches/region/{region}', [CinemaController::class, 'getAllCinemaBranchByRegion'])->name('apiCinemas.branches.regions.getAll');

    Route::get('films/options', [FilmController::class, 'getListOptionsFilm'])->name('apiFilms.options');
    Route::get('films/{id}', [FilmController::class, 'getFilmDetails'])->name('apiFilms.show');

    Route::get('showtimes/branch/{branch}/date/{date}', [ShowtimesController::class, 'getShowtimesByDateAndCinemaBranch'])->name('showtimes.getByDateAndBranch');
    Route::get('showtimes/{showtime}/seating-arrangement', [ShowtimesController::class, 'getSeatingLayoutByShowtime'])->name('showtimes.getSeatLayoutByShowtime');

    Route::get('/auditoria/{auditorium}', [AuditoriumController::class, 'getSeatTypes'])->name('apiAuditoria.getSeatTypes');
});
