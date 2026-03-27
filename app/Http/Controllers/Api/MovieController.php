<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\TmdbRequestException;
use App\Http\Controllers\Controller;
use App\Http\Helpers\TmdbResponseHelper;
use App\Http\Requests\Movie\FindMovieRequest;
use App\Http\Requests\Movie\MovieBasicRequest;
use App\Services\TmdbClient;
use Illuminate\Http\JsonResponse;
use Throwable;

class MovieController extends Controller
{
    public function __construct(protected TmdbClient $client) {}

    public function find(FindMovieRequest $request): JsonResponse
    {
        $movieId = $request->validated()['movie_id'];

        $params = $request->filled('append_to_response')
            ? ['append_to_response' => $request->input('append_to_response')]
            : [];

        return $this->handleTmdb(
            fn() => $this->client->movie($movieId, $params),
            'Movie not found.'
        );
    }

    public function watchlist(): JsonResponse
    {
        return $this->handleTmdb(
            fn() => $this->client->movieWatchlist(),
            'Movie watchlist not found.'
        );
    }

    public function credits(MovieBasicRequest $request): JsonResponse
    {
        $movieId = $request->validated()['movie_id'];

        return $this->handleTmdb(
            fn() => $this->client->movieCredits($movieId),
            'Movie credits not found.'
        );
    }

    public function images(MovieBasicRequest $request): JsonResponse
    {
        $movieId = $request->validated()['movie_id'];

        return $this->handleTmdb(
            fn() => $this->client->movieImages($movieId),
            'Movie images not found.'
        );
    }

    public function videos(MovieBasicRequest $request): JsonResponse
    {
        $movieId = $request->validated()['movie_id'];

        return $this->handleTmdb(
            fn() => $this->client->movieVideos($movieId),
            'Movie videos not found.'
        );
    }

    public function recommendations(MovieBasicRequest $request): JsonResponse
    {
        $movieId = $request->validated()['movie_id'];

        return $this->handleTmdb(
            fn() => $this->client->movieRecommendations($movieId),
            'Movie recommendations not found.'
        );
    }

    public function similar(MovieBasicRequest $request): JsonResponse
    {
        $movieId = $request->validated()['movie_id'];

        return $this->handleTmdb(
            fn() => $this->client->similarMovies($movieId),
            'Similar movies not found.'
        );
    }

    public function nowPlaying(): JsonResponse
    {
        return $this->handleTmdb(
            fn() => $this->client->nowPlayingMovies(),
            'Now playing movies not found.'
        );
    }

    public function popular(): JsonResponse
    {
        return $this->handleTmdb(
            fn() => $this->client->popularMovies(),
            'Popular movies not found.'
        );
    }

    public function topRated(): JsonResponse
    {
        return $this->handleTmdb(
            fn() => $this->client->topRatedMovies(),
            'Top rated movies not found.'
        );
    }

    public function upcoming(): JsonResponse
    {
        return $this->handleTmdb(
            fn() => $this->client->upcomingMovies(),
            'Upcoming movies not found.'
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
