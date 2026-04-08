<?php

use App\Http\Controllers\Api\TmdbMovieController;
use App\Http\Controllers\Api\TmdbSearchController;
use App\Http\Controllers\Api\TmdbTrendingController;
use App\Http\Controllers\Api\TmdbTvController;
use App\Http\Controllers\Api\TmdbWatchlistController;
use Illuminate\Support\Facades\Route;

Route::prefix('/api')->group(function () {
  Route::prefix('/search')->group(function () {
    Route::get('/multi', [TmdbSearchController::class, 'multi'])->name('api.search.multi');
    Route::get('/movie', [TmdbSearchController::class, 'movie'])->name('api.search.movie');
    Route::get('/tv', [TmdbSearchController::class, 'tv'])->name('api.search.tv');
    Route::get('/person', [TmdbSearchController::class, 'person'])->name('api.search.person');
    Route::get('/collection', [TmdbSearchController::class, 'collection'])->name('api.search.collection');
    Route::get('/company', [TmdbSearchController::class, 'company'])->name('api.search.company');
    Route::get('/keyword', [TmdbSearchController::class, 'keyword'])->name('api.search.keyword');
  });

  Route::prefix('/trending')->group(function () {
    Route::get('/all/{time_window}', [TmdbTrendingController::class, 'all'])->name('api.trending.all');
    Route::get('/movie/{time_window}', [TmdbTrendingController::class, 'movie'])->name('api.trending.movie');
    Route::get('/tv/{time_window}', [TmdbTrendingController::class, 'tv'])->name('api.trending.tv');
    Route::get('/person/{time_window}', [TmdbTrendingController::class, 'person'])->name('api.trending.person');
  });

  Route::prefix('/watchlist')->group(function () {
    Route::get('/movie', [TmdbWatchlistController::class, 'movie'])->name('api.watchlist.movie');
    Route::get('/tv', [TmdbWatchlistController::class, 'tv'])->name('api.watchlist.tv');

    Route::post('/', [TmdbWatchlistController::class, 'toggle'])->name('api.watchlist.toggle');
    Route::post('/sync_title', [TmdbWatchlistController::class, 'syncTitle'])->name('api.watchlist.sync_title');
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
    Route::get('/airing_today', [TmdbTvController::class, 'airingToday'])->name('api.tv.airing_today');
    Route::get('/on_the_air', [TmdbTvController::class, 'onTheAir'])->name('api.tv.on_the_air');
    Route::get('/popular', [TmdbTvController::class, 'popular'])->name('api.tv.popular');
    Route::get('/top_rated', [TmdbTvController::class, 'topRated'])->name('api.tv.top_rated');

    Route::get('/{tv_id}', [TmdbTvController::class, 'show'])->name('api.tv.show');
    Route::get('/{tv_id}/credits', [TmdbTvController::class, 'credits'])->name('api.tv.credits');
    Route::get('/{tv_id}/images', [TmdbTvController::class, 'images'])->name('api.tv.images');
    Route::get('/{tv_id}/videos', [TmdbTvController::class, 'videos'])->name('api.tv.videos');
    Route::get('/{tv_id}/recommendations', [TmdbTvController::class, 'recommendations'])->name('api.tv.recommendations');
    Route::get('/{tv_id}/similar', [TmdbTvController::class, 'similar'])->name('api.tv.similar');
  });
});
