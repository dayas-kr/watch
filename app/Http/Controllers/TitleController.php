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
            'watchlist' =>  $this->getWatchlist(),
            'inWatchlist' => $this->inList($movie_id, MediaType::MOVIE, ListType::WATCHLIST),
            'inWatched' => $this->inList($movie_id, MediaType::MOVIE, ListType::WATCHED)
        ];

        return view('movie.show', compact('data'));
    }

    public function tv($tv_id)
    {
        $data = [
            'id' => (int) $tv_id,
            'watchlist' =>  $this->getWatchlist(),
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

    private function getWatchlist(): array
    {
        $userId = Auth::id();
        if (!$userId) return ['movie' => collect(), 'tv' => collect()];

        $items = UserList::defaultOfType($userId, ListType::WATCHLIST)
            ->items()
            ->pluck('media_id', 'media_type')
            ->groupBy('media_type');

        $items = UserList::defaultOfType($userId, ListType::WATCHLIST)
            ->items()
            ->get(['media_id', 'media_type'])
            ->groupBy('media_type')
            ->map(fn($group) => $group->pluck('media_id'));

        return [
            'movie' => $items->get(MediaType::MOVIE, collect()),
            'tv'    => $items->get(MediaType::TV, collect()),
        ];
    }
}
