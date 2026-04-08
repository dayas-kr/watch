<?php

namespace App\Http\Controllers;

use App\Models\ListType;
use App\Models\MediaType;
use App\Models\UserList;
use Illuminate\Support\Facades\Auth;

class TitleController extends Controller
{
    public function movie($movie_id)
    {
        $data = [
            'id' => (int) $movie_id,
            'inWatchlist' => $this->inWatchlist($movie_id, MediaType::MOVIE)
        ];

        return view('movie.show', compact('data'));
    }

    public function tv($tv_id)
    {
        $data = [
            'id' => (int) $tv_id,
            'inWatchlist' => $this->inWatchlist($tv_id, MediaType::TV)
        ];

        return view('tv.show', compact('data'));
    }

    private function inWatchlist($mediaId, $mediaType)
    {
        return UserList::defaultOfType(Auth::id(), ListType::WATCHLIST)
            ->items()
            ->where(['media_id' => $mediaId, 'media_type' => $mediaType])
            ->exists();
    }
}
