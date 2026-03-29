<?php

use App\Http\Controllers\Api\TmdbSearchController;
use Illuminate\Support\Facades\Route;

Route::prefix('/api')->middleware(['auth', 'ajax'])->group(function () {
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
    // TODO: watchlist routes
  });

  Route::prefix('/movie')->group(function () {
    // TODO: static & dynamic movie routes
  });

  Route::prefix('/tv')->group(function () {
    // TODO: static & dynamic tv routes
  });
});
