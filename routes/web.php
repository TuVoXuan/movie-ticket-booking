<?php

use App\Http\Controllers\FilmController;
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
Route::get('/films', function () {
    return Inertia::render('FilmsPage');
})->name('films');
Route::get('/artists', function () {
    return Inertia::render('ArtistsPage');
})->name('artists');
Route::get('/genres', function () {
    return Inertia::render('GenresPage');
})->name('genres');
Route::get('/cinemas', function () {
    return Inertia::render('CinemasPage');
})->name('cinemas');
Route::get('/roles', function () {
    return Inertia::render('RolesPage');
})->name('roles');
Route::get('/permissions', function () {
    return Inertia::render('PermissionsPage');
})->name('permissions');
Route::get('/users', function () {
    return Inertia::render('UsersPage');
})->name('users');


// Route::resource('films', FilmController::class)->only(['index', 'create']);

// Route::get('/artists', function () {
//     return view('artist.index');
// })->name('artists');
// Route::get('/genres', function () {
//     return view('genre.index');
// })->name('genres');
// Route::get('/cinemas', function () {
//     return view('cinema.index');
// })->name('cinemas');
// Route::get('/roles', function () {
//     return view('role.index');
// })->name('roles');
// Route::get('/permissions', function () {
//     return view('permission.index');
// })->name('permissions');
// Route::get('/users', function () {
//     return view('user.index');
// })->name('users.index');

// Route::get('/users/{user}', function () {
//     return view('user.show');
// })->name('users.show');
