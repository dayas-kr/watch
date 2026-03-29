<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\HandlesTmdb;
use App\Http\Controllers\Controller;
use App\Http\Requests\Watchlist\WatchlistRequest;
use App\Services\TmdbClient;
use App\Support\Concerns\BuildsQuery;
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
}
