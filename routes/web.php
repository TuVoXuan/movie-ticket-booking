<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\AuditoriumController;
use App\Http\Controllers\AuthController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Route::resource('films', FilmController::class)
    //     ->only(['index', 'create', 'store', 'edit', 'destroy', 'update']);

    Route::get('films', [FilmController::class, 'index'])->name('films.index')->middleware('permission:films.read');
    Route::post('films', [FilmController::class, 'store'])->name('films.store')->middleware('permission:films.create');
    Route::get('films/create', [FilmController::class, 'create'])->name('films.create')->middleware('permission:films.create');
    Route::put('films/{film}', [FilmController::class, 'update'])->name('films.update')->middleware('permission:films.update');
    Route::delete('films/{film}', [FilmController::class, 'destroy'])->name('films.destroy')->middleware('permission:films.delete');
    Route::get('films/{film}/edit', [FilmController::class, 'edit'])->name('films.edit')->middleware('permission:films.update');

    // Route::resource('artists', ArtistController::class)
    //     ->only(['index', 'create', 'store', 'edit', 'destroy', 'update']);
    Route::get('artists', [ArtistController::class, 'index'])->name('artists.index')->middleware('permission:artists.read');
    Route::post('artists', [ArtistController::class, 'store'])->name('artists.store')->middleware('permission:artists.create');
    Route::get('artists/create', [ArtistController::class, 'create'])->name('artists.create')->middleware('permission:artists.create');
    Route::put('artists/{film}', [ArtistController::class, 'update'])->name('artists.update')->middleware('permission:artists.update');
    Route::delete('artists/{film}', [ArtistController::class, 'destroy'])->name('artists.destroy')->middleware('permission:artists.delete');
    Route::get('artists/{film}/edit', [ArtistController::class, 'edit'])->name('artists.edit')->middleware('permission:artists.update');

    // Route::resource('genres', GenreController::class)
    //     ->only(['index', 'store', 'destroy', 'update']);
    Route::get('genres', [GenreController::class, 'index'])->name('genres.index')->middleware('permission:genres.read');
    Route::post('genres', [GenreController::class, 'store'])->name('genres.store')->middleware('permission:genres.create');
    Route::put('genres/{film}', [GenreController::class, 'update'])->name('genres.update')->middleware('permission:genres.update');
    Route::delete('genres/{film}', [GenreController::class, 'destroy'])->name('genres.destroy')->middleware('permission:genres.delete');

    Route::prefix('cinemas')->group(function () {
        Route::get('/companies', [CinemaController::class, 'companies'])->name('cinemas.companies.index')->middleware('permission:cinemas_companies.read');
        Route::post('/companies', [CinemaController::class, 'storeCinema'])->name('cinemas.companies.store')->middleware('permission:cinemas_companies.create');
        Route::put('/companies/{company}', [CinemaController::class, 'updateCinema'])->name('cinemas.companies.update')->middleware('permission:cinemas_companies.update');
        Route::delete('/companies/{company}', [CinemaController::class, 'destroyCinema'])->name('cinemas.companies.destroy')->middleware('permission:cinemas_companies.delete');

        Route::get('/branches', [CinemaController::class, 'branches'])->name('cinemas.branches.index')->middleware('permission:cinemas_branches.read');
        Route::get('/branches/create', [CinemaController::class, 'createBranch'])->name('cinemas.branches.create')->middleware('permission:cinemas_branches.create');
        Route::post('/branches', [CinemaController::class, 'storeBranch'])->name('cinemas.branches.store')->middleware('permission:cinemas_branches.create');
        Route::get('/branches/{branch}/edit', [CinemaController::class, 'editBranch'])->name('cinemas.branches.edit')->middleware('permission:cinemas_branches.update');
        Route::put('/branches/{branch}', [CinemaController::class, 'updateBranch'])->name('cinemas.branches.update')->middleware('permission:cinemas_branches.update');

        Route::get('/branches/{branch}/auditoria', [AuditoriumController::class, 'index'])->name('cinemas.branches.auditoria.index')->middleware('permission:auditoria.read');
        Route::get('/branches/{branch}/auditoria/create', [AuditoriumController::class, 'create'])->name('cinemas.branches.auditoria.create')->middleware('permission:auditoria.create');
        Route::post('/branches/{branch}/auditoria', [AuditoriumController::class, 'store'])->name('cinemas.branches.auditoria.store')->middleware('permission:auditoria.create');
        Route::get('/branches/{branch}/auditoria/{auditorium}/edit', [AuditoriumController::class, 'edit'])->name('cinemas.branches.auditoria.edit')->middleware('permission:auditoria.update');
        Route::put('/branches/{branch}/auditoria/{auditorium}', [AuditoriumController::class, 'update'])->name('cinemas.branches.auditoria.update')->middleware('permission:auditoria.update');

        Route::get('/branches/{branch}/showtimes', [ShowtimesController::class, 'index'])->name('cinemas.branches.showtimes.index')->middleware('permission:showtimes.read');
        Route::get('/branches/{branch}/showtimes/create', [ShowtimesController::class, 'create'])->name('cinemas.branches.showtimes.create')->middleware('permission:showtimes.create');
        Route::post('/branches/{branch}/showtimes', [ShowtimesController::class, 'store'])->name('cinemas.branches.showtimes.store')->middleware('permission:showtimes.create');
        Route::get('/branches/{branch}/showtimes/{showtime}/edit', [ShowtimesController::class, 'edit'])->name('cinemas.branches.showtimes.edit')->middleware('permission:showtimes.update');
        Route::put('/branches/{branch}/showtimes/{showtime}', [ShowtimesController::class, 'update'])->name('cinemas.branches.showtimes.update')->middleware('permission:showtimes.update');
    });

    // Route::resource('roles', RoleController::class)
    //     ->only(['index', 'store', 'update', 'destroy']);
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index')->middleware('permission:roles.read');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:roles.create');
    Route::put('roles/{film}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:roles.update');
    Route::delete('roles/{film}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:roles.delete');

    Route::prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('permissions.index')->middleware('permission:permissions.read');
        Route::post('/role-permissions', [PermissionController::class, 'updateRolePermission'])->name('permissions.updateRolePermissions')->middleware('permission:permissions.update_role_permissions');
    });

    // Route::resource('users', UserController::class)
    //     ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::get('users', [UserController::class, 'index'])->name('users.index')->middleware('permission:users.read');
    Route::post('users', [UserController::class, 'store'])->name('users.store')->middleware('permission:users.create');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:users.create');
    Route::put('users/{film}', [UserController::class, 'update'])->name('users.update')->middleware('permission:users.update');
    Route::delete('users/{film}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:users.delete');
    Route::get('users/{film}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:users.update');
});

Route::get('/login', [AuthController::class, 'getLogin'])->name('auth.getLogin');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
