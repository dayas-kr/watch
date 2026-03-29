<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/api')->middleware(['auth', 'ajax'])->group(function () {
  Route::prefix('/search')->group(function () {
    // TODO: search routes
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
