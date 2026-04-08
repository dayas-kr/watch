<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\HandlesTmdb;
use App\Http\Controllers\Controller;
use App\Http\Requests\Watchlist\ToggleWatchlistRequest;
use App\Http\Requests\Watchlist\WatchlistRequest;
use App\Models\ListType;
use App\Models\MediaType;
use App\Models\UserList;
use App\Services\TmdbClient;
use App\Support\Concerns\BuildsQuery;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;

class TmdbWatchlistController extends Controller
{
    use HandlesTmdb, BuildsQuery;

    public function __construct(protected TmdbClient $client) {}

    public function movie(WatchlistRequest $request): JsonResponse
    {
        $query = $this->buildQuery($request, [
            'language',
            'page',
            'session_id',
            'sort_by'
        ]);

        $result = $this->client->movieWatchlist($query);

        return $this->handleTmdb(fn() => $result, 'No movies found in your watchlist.');
    }

    public function tv(WatchlistRequest $request): JsonResponse
    {
        $query = $this->buildQuery($request, [
            'language',
            'page',
            'session_id',
            'sort_by'
        ]);

        $result = $this->client->tvWatchlist($query);

        return $this->handleTmdb(fn() => $result, 'No TV shows found in your watchlist.');
    }

    public function toggle(ToggleWatchlistRequest $request): JsonResponse
    {
        $query = $this->buildQuery($request, ['media_type', 'media_id', 'watchlist']);

        return $this->handleTmdb(fn() => $this->client->toggleWatchlist($query));
    }

    public function syncTitle(ToggleWatchlistRequest $request): JsonResponse
    {
        $mediaType = MediaType::where('name', $request->media_type)->value('id');

        if (!$mediaType) {
            return response()->json(['success' => false, 'message' => 'Invalid media type: ' . $request->media_type], 422);
        }

        $list     = UserList::defaultOfType(Auth::id(), ListType::WATCHLIST);
        $criteria = ['media_id' => $request->media_id, 'media_type' => $mediaType];

        $request->watchlist
            ? $list->items()->firstOrCreate($criteria)
            : $list->items()->where($criteria)->delete();

        return response()->json(['success' => true, 'message' => 'Title synced successfully.']);
    }
}
