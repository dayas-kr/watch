<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\TmdbRequestException;
use App\Http\Controllers\Controller;
use App\Http\Helpers\TmdbResponseHelper;
use App\Http\Requests\Tv\FindTvShowRequest;
use App\Http\Requests\Tv\TvBasicRequest;
use App\Services\TmdbClient;
use Illuminate\Http\JsonResponse;
use Throwable;

class TvShowController extends Controller
{
    public function __construct(protected TmdbClient $client) {}

    public function find(FindTvShowRequest $request): JsonResponse
    {
        $tvId = $request->validated()['tv_id'];

        $params = $request->filled('append_to_response')
            ? ['append_to_response' => $request->input('append_to_response')]
            : [];

        return $this->handleTmdb(
            fn() => $this->client->tvShow($tvId, $params),
            'TV show not found.'
        );
    }

    public function watchlist(): JsonResponse
    {
        return $this->handleTmdb(
            fn() => $this->client->tvShowWatchlist(),
            'TV show watchlist not found.'
        );
    }

    public function credits(TvBasicRequest $request): JsonResponse
    {
        $tvId = $request->validated()['tv_id'];

        return $this->handleTmdb(
            fn() => $this->client->tvShowCredits($tvId),
            'TV show credits not found.'
        );
    }

    public function images(TvBasicRequest $request): JsonResponse
    {
        $tvId = $request->validated()['tv_id'];

        return $this->handleTmdb(
            fn() => $this->client->tvShowImages($tvId),
            'TV show images not found.'
        );
    }

    public function videos(TvBasicRequest $request): JsonResponse
    {
        $tvId = $request->validated()['tv_id'];

        return $this->handleTmdb(
            fn() => $this->client->tvShowVideos($tvId),
            'TV show videos not found.'
        );
    }

    public function recommendations(TvBasicRequest $request): JsonResponse
    {
        $tvId = $request->validated()['tv_id'];

        return $this->handleTmdb(
            fn() => $this->client->tvShowRecommendations($tvId),
            'TV show recommendations not found.'
        );
    }

    public function similar(TvBasicRequest $request): JsonResponse
    {
        $tvId = $request->validated()['tv_id'];

        return $this->handleTmdb(
            fn() => $this->client->similarTvShows($tvId),
            'Similar TV shows not found.'
        );
    }
    public function airingToday(TvBasicRequest $request): JsonResponse
    {
        $tvId = $request->validated()['tv_id'];

        return $this->handleTmdb(
            fn() => $this->client->airingTodayTvShows($tvId),
            'TV shows airing today not found.'
        );
    }
    public function onTheAir(TvBasicRequest $request): JsonResponse
    {
        $tvId = $request->validated()['tv_id'];

        return $this->handleTmdb(
            fn() => $this->client->onTheAirTvShows($tvId),
            'TV shows on the air not found.'
        );
    }
    public function popular(TvBasicRequest $request): JsonResponse
    {
        $tvId = $request->validated()['tv_id'];

        return $this->handleTmdb(
            fn() => $this->client->popularTvShows($tvId),
            'popular TV shows not found.'
        );
    }
    public function topRated(TvBasicRequest $request): JsonResponse
    {
        $tvId = $request->validated()['tv_id'];

        return $this->handleTmdb(
            fn() => $this->client->topRatedTvShows($tvId),
            'Top rated TV shows not found.'
        );
    }

    /**
     * Centralized TMDB handler
     */
    private function handleTmdb(callable $callback, string $errorMessage): JsonResponse
    {
        try {
            return TmdbResponseHelper::handleSuccess($callback());
        } catch (TmdbRequestException $e) {
            return TmdbResponseHelper::handleException($e, $errorMessage);
        } catch (Throwable $e) {
            logger()->error($e);
            return TmdbResponseHelper::handleServerError();
        }
    }
}
