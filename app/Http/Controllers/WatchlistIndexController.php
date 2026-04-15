<?php

namespace App\Http\Controllers;

class WatchlistIndexController extends Controller
{
    public function __invoke()
    {
        return view('watchlist.index');
    }
}
