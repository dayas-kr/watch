<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TitleController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', WelcomeController::class)->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth required
Route::middleware(['auth'])->group(function () {
    Route::get('/watchlist', function () {
        return view('watchlist.index');
    })->name('watchlist.index');

    Route::get('/movie/{movie_id}', [TitleController::class, 'movie'])->name('movie.show');
    Route::get('/tv/{tv_id}', [TitleController::class, 'tv'])->name('tv.show');
});

require __DIR__ . '/auth.php';

require __DIR__ . '/ajax.php';
