<?php

use App\Http\Controllers\Api\TmdbMovieController;
use App\Http\Controllers\Api\TmdbSearchController;
use App\Http\Controllers\Api\TmdbWatchlistController;
use Illuminate\Support\Facades\Route;

Route::prefix('/api')->middleware(['ajax', 'auth'])->group(function () {
  Route::prefix('/search')->group(function () {
    Route::get('/multi', [TmdbSearchController::class, 'multi'])->name('api.search.multi');
    Route::get('/movie', [TmdbSearchController::class, 'movie'])->name('api.search.movie');
    Route::get('/tv', [TmdbSearchController::class, 'tv'])->name('api.search.tv');
    Route::get('/person', [TmdbSearchController::class, 'person'])->name('api.search.person');
    Route::get('/collection', [TmdbSearchController::class, 'collection'])->name('api.search.collection');
    Route::get('/company', [TmdbSearchController::class, 'company'])->name('api.search.company');
    Route::get('/keyword', [TmdbSearchController::class, 'keyword'])->name('api.search.keyword');
  });

  Route::prefix('/watchlist')->group(function () {
    Route::get('/movie', [TmdbWatchlistController::class, 'movie'])->name('api.watchlist.movie');
    Route::get('/tv', [TmdbWatchlistController::class, 'tv'])->name('api.watchlist.tv');
  });

  Route::prefix('/movie')->group(function () {
    Route::get('/now_playing', [TmdbMovieController::class, 'nowPlaying'])->name('api.movie.now_playing');
    Route::get('/popular', [TmdbMovieController::class, 'popular'])->name('api.movie.popular');
    Route::get('/top_rated', [TmdbMovieController::class, 'topRated'])->name('api.movie.top_rated');
    Route::get('/upcoming', [TmdbMovieController::class, 'upcoming'])->name('api.movie.upcoming');

    Route::get('/{movie_id}', [TmdbMovieController::class, 'show'])->name('api.movie.show');
    Route::get('/{movie_id}/credits', [TmdbMovieController::class, 'credits'])->name('api.movie.credits');
    Route::get('/{movie_id}/images', [TmdbMovieController::class, 'images'])->name('api.movie.images');
    Route::get('/{movie_id}/videos', [TmdbMovieController::class, 'videos'])->name('api.movie.videos');
    Route::get('/{movie_id}/recommendations', [TmdbMovieController::class, 'recommendations'])->name('api.movie.recommendations');
    Route::get('/{movie_id}/similar', [TmdbMovieController::class, 'similar'])->name('api.movie.similar');
  });

  Route::prefix('/tv')->group(function () {
    // TODO: static & dynamic tv routes
  });
});
