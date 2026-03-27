<?php

use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\TvShowController;
use Illuminate\Support\Facades\Route;

Route::prefix('/api')->middleware(['auth', 'ajax'])->group(function () {
  Route::prefix('/movie')->group(function () {
    Route::get('/watchlist',   [MovieController::class, 'watchlist']);
    Route::get('/now_playing', [MovieController::class, 'nowPlaying']);
    Route::get('/popular',     [MovieController::class, 'popular']);
    Route::get('/top_rated',   [MovieController::class, 'topRated']);
    Route::get('/upcoming',    [MovieController::class, 'upcoming']);

    Route::get('/{id}',               [MovieController::class, 'find']);
    Route::get('/{id}/credits',       [MovieController::class, 'credits']);
    Route::get('/{id}/images',        [MovieController::class, 'images']);
    Route::get('/{id}/videos',        [MovieController::class, 'videos']);
    Route::get('/{id}/recommendations', [MovieController::class, 'recommendations']);
    Route::get('/{id}/similar',       [MovieController::class, 'similar']);
  });

  Route::prefix('/tv')->group(function () {
    Route::get('/watchlist',   [TvShowController::class, 'watchlist']);
    Route::get('/airing_today', [TvShowController::class, 'airingToday']);
    Route::get('/on_the_air',  [TvShowController::class, 'onTheAir']);
    Route::get('/popular',     [TvShowController::class, 'popular']);
    Route::get('/top_rated',   [TvShowController::class, 'topRated']);

    Route::get('/{id}',               [TvShowController::class, 'find']);
    Route::get('/{id}/credits',       [TvShowController::class, 'credits']);
    Route::get('/{id}/images',        [TvShowController::class, 'images']);
    Route::get('/{id}/videos',        [TvShowController::class, 'videos']);
    Route::get('/{id}/recommendations', [TvShowController::class, 'recommendations']);
    Route::get('/{id}/similar',       [TvShowController::class, 'similar']);
  });
});
