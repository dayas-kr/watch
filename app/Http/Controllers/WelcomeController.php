<?php

namespace App\Http\Controllers;

use App\Models\ListType;
use App\Models\MediaType;
use App\Models\UserList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = [
            'watchlist' => $this->getWatchlist(),
        ];

        return view('welcome', compact('data'));
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
