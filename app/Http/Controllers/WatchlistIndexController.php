<?php

namespace App\Http\Controllers;

use App\Models\ListItem;
use App\Models\ListType;
use App\Models\MediaType;
use App\Models\UserList;
use Illuminate\Support\Facades\Auth;

class WatchlistIndexController extends Controller
{
    public function __invoke()
    {
        $data = [
            'watchlist' => $this->getWatchlist(),
            'user_lists' => $this->getUserlists(),
        ];

        return view('watchlist.index', compact('data'));
    }

    private function getWatchlist(): array
    {
        $userId = Auth::id();
        if (!$userId) return ['movie' => collect(), 'tv' => collect()];

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

    private function getUserLists(): array
    {
        $userId = Auth::id();
        if (!$userId) return [];

        return UserList::where('user_id', $userId)
            ->where('list_type', ListType::CUSTOM)
            ->with(['items' => fn($q) => $q->select('list_id', 'media_id', 'media_type')])
            ->get(['id', 'name'])
            ->map(fn(UserList $list) => [
                'id'    => $list->id,
                'name'  => $list->name,
                'items' => $list->items->map(fn(ListItem $item) => [
                    'media_id'   => $item->media_id,
                    'media_type' => $item->media_type === MediaType::MOVIE ? 'movie' : 'tv',
                ])->all(),
            ])
            ->all();
    }
}
