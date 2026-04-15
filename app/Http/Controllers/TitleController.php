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
            'watchlist' => $this->getList(ListType::WATCHLIST),
            'favorites' => $this->getList(ListType::FAVORITES),
            'inWatchlist' => $this->inList($movie_id, MediaType::MOVIE, ListType::WATCHLIST),
            'inWatched' => $this->inList($movie_id, MediaType::MOVIE, ListType::WATCHED)
        ];

        return view('movie.show', compact('data'));
    }

    public function tv($tv_id)
    {
        $data = [
            'id' => (int) $tv_id,
            'watchlist' => $this->getList(ListType::WATCHLIST),
            'favorites' => $this->getList(ListType::FAVORITES),
            'watched' => $this->getList(ListType::WATCHED),
            'inWatchlist' => $this->inList($tv_id, MediaType::TV, ListType::WATCHLIST),
            'inWatched' => $this->inList($tv_id, MediaType::TV, ListType::WATCHED)
        ];

        return view('tv.show', compact('data'));
    }

    private function inList($mediaId, $mediaType, $listType)
    {
        $userId = Auth::id();

        if (!$userId) {
            return false;
        }
        return UserList::defaultOfType($userId, $listType)
            ->items()
            ->where(['media_id' => $mediaId, 'media_type' => $mediaType])
            ->exists();
    }

    private function getList($listType)
    {
        $userId = Auth::id();

        if (!$userId) {
            return collect();
        }

        return UserList::defaultOfType($userId, $listType)
            ->items()
            ->with('mediaType')
            ->pluck('media_id');
    }
}
