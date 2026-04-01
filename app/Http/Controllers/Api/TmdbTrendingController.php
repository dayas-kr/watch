<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\HandlesTmdb;
use App\Http\Controllers\Controller;
use App\Http\Requests\Trending\TrendingRequest;
use App\Services\TmdbClient;
use App\Support\Concerns\BuildsQuery;
use Illuminate\Http\JsonResponse;

class TmdbTrendingController extends Controller
{
    use HandlesTmdb, BuildsQuery;

    public function __construct(protected TmdbClient $client) {}

    public function all(TrendingRequest $request): JsonResponse
    {
        $timeWindow = $request->validated()['time_window'];

        $query = $this->buildQuery($request, ['language']);

        $result = $this->client->trendingTitles($timeWindow, $query);

        return $this->handleTmdb(fn() => $result);
    }

    public function movie(TrendingRequest $request): JsonResponse
    {
        $timeWindow = $request->validated()['time_window'];

        $query = $this->buildQuery($request, ['language']);

        $result = $this->client->trendingMovies($timeWindow, $query);

        return $this->handleTmdb(fn() => $result);
    }

    public function tv(TrendingRequest $request): JsonResponse
    {
        $timeWindow = $request->validated()['time_window'];

        $query = $this->buildQuery($request, ['language']);

        $result = $this->client->trendingTv($timeWindow, $query);

        return $this->handleTmdb(fn() => $result);
    }

    public function person(TrendingRequest $request): JsonResponse
    {
        $timeWindow = $request->validated()['time_window'];

        $query = $this->buildQuery($request, ['language']);

        $result = $this->client->trendingPeople($timeWindow, $query);

        return $this->handleTmdb(fn() => $result);
    }
}
