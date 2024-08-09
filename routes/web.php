<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\AuditoriumController;
use App\Http\Controllers\CinemaController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ShowtimesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::resource('films', FilmController::class)
    ->only(['index', 'create', 'store', 'edit', 'destroy', 'update']);

Route::resource('artists', ArtistController::class)
    ->only(['index', 'create', 'store', 'edit', 'destroy', 'update']);

Route::resource('genres', GenreController::class)
    ->only(['index', 'store', 'destroy', 'update']);

Route::prefix('cinemas')->group(function () {
    Route::get('/companies', [CinemaController::class, 'companies'])->name('cinemas.companies.index');
    Route::post('/companies', [CinemaController::class, 'storeCinema'])->name('cinemas.companies.store');
    Route::put('/companies/{company}', [CinemaController::class, 'updateCinema'])->name('cinemas.companies.update');
    Route::delete('/companies/{company}', [CinemaController::class, 'destroyCinema'])->name('cinemas.companies.destroy');

    Route::get('/branches', [CinemaController::class, 'branches'])->name('cinemas.branches.index');
    Route::get('/branches/create', [CinemaController::class, 'createBranch'])->name('cinemas.branches.create');
    Route::post('/branches', [CinemaController::class, 'storeBranch'])->name('cinemas.branches.store');
    Route::get('/branches/{branch}/edit', [CinemaController::class, 'editBranch'])->name('cinemas.branches.edit');
    Route::put('/branches/{branch}', [CinemaController::class, 'updateBranch'])->name('cinemas.branches.update');

    Route::get('/branches/{branch}/auditoria', [AuditoriumController::class, 'index'])->name('cinemas.branches.auditoria.index');
    Route::get('/branches/{branch}/auditoria/create', [AuditoriumController::class, 'create'])->name('cinemas.branches.auditoria.create');
    Route::post('/branches/{branch}/auditoria', [AuditoriumController::class, 'store'])->name('cinemas.branches.auditoria.store');
    Route::get('/branches/{branch}/auditoria/{auditorium}/edit', [AuditoriumController::class, 'edit'])->name('cinemas.branches.auditoria.edit');
    Route::put('/branches/{branch}/auditoria/{auditorium}', [AuditoriumController::class, 'update'])->name('cinemas.branches.auditoria.update');

    Route::get('/branches/{branch}/showtimes', [ShowtimesController::class, 'index'])->name('cinemas.branches.showtimes.index');
    Route::get('/branches/{branch}/showtimes/create', [ShowtimesController::class, 'create'])->name('cinemas.branches.showtimes.create');
    Route::post('/branches/{branch}/showtimes', [ShowtimesController::class, 'store'])->name('cinemas.branches.showtimes.store');
    Route::get('/branches/{branch}/showtimes/{showtime}/edit', [ShowtimesController::class, 'edit'])->name('cinemas.branches.showtimes.edit');
    Route::put('/branches/{branch}/showtimes/{showtime}', [ShowtimesController::class, 'update'])->name('cinemas.branches.showtimes.update');
});

Route::resource('roles', RoleController::class)
    ->only(['index', 'store', 'update', 'destroy']);

Route::prefix('permissions')->group(function () {
    Route::get('/', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/role-permissions', [PermissionController::class, 'updateRolePermission'])->name('permissions.updateRolePermissions');
});

Route::resource('users', UserController::class)
    ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
