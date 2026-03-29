<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\HandlesTmdb;
use App\Http\Controllers\Controller;
use App\Http\Requests\Movie\MovieRequest;
use App\Http\Requests\Title\TitleListsRequest;
use App\Services\TmdbClient;
use App\Support\Concerns\QueryParam;
use App\Support\TmdbAppend;
use Illuminate\Http\JsonResponse;

class TmdbMovieController extends Controller
{
    use HandlesTmdb, QueryParam;

    public function __construct(protected TmdbClient $client) {}

    public function nowPlaying(TitleListsRequest $request): JsonResponse
    {
        $request->validate([
            'region' => 'nullable|string',
        ]);

        $query = $this->buildQuery($request, ['language', 'page', 'region']);

        $result = $this->client->nowPlayingMovies($query);

        return $this->handleTmdb(fn() => $result, 'No movies are currently playing for the given criteria.');
    }

    public function popular(TitleListsRequest $request): JsonResponse
    {
        $request->validate([
            'region' => 'nullable|string',
        ]);

        $query = $this->buildQuery($request, ['language', 'page', 'region']);

        $result = $this->client->popularMovies($query);

        return $this->handleTmdb(fn() => $result, 'No popular movies found for the given criteria.');
    }

    public function topRated(TitleListsRequest $request): JsonResponse
    {
        $request->validate([
            'region' => 'nullable|string',
        ]);

        $query = $this->buildQuery($request, ['language', 'page', 'region']);

        $result = $this->client->topRatedMovies($query);

        return $this->handleTmdb(fn() => $result, 'No top-rated movies found for the given criteria.');
    }

    public function upcoming(TitleListsRequest $request): JsonResponse
    {
        $request->validate([
            'region' => 'nullable|string',
        ]);

        $query = $this->buildQuery($request, ['language', 'page', 'region']);

        $result = $this->client->upcomingMovies($query);

        return $this->handleTmdb(fn() => $result, 'No upcoming movies found for the given criteria.');
    }

    public function show(MovieRequest $request): JsonResponse
    {
        $movieId = $request->validated()['movie_id'];

        $request->validate(
            [
                'language' => 'nullable|string',
                'append_to_response' => [
                    'nullable',
                    'string',
                    'regex:' . TmdbAppend::regex(TmdbAppend::movie()),
                ],
            ],
            [
                'append_to_response.regex' => 'Invalid format. Use comma-separated values. Supported values: ' . implode(', ', TmdbAppend::movie()) . '.',
            ]
        );

        $query = $this->buildQuery($request, ['language', 'append_to_response']);

        $result = $this->client->movie($movieId, $query);

        return $this->handleTmdb(fn() => $result, 'Movie not found. Please verify the provided movie ID.');
    }

    public function credits(MovieRequest $request): JsonResponse
    {
        $movieId = $request->validated()['movie_id'];

        $request->validate([
            'language' => 'nullable|string',
        ]);

        $query = $this->buildQuery($request, ['language']);

        $result = $this->client->movieCredits($movieId, $query);

        return $this->handleTmdb(fn() => $result, 'No credits found for this movie.');
    }

    public function images(MovieRequest $request): JsonResponse
    {
        $movieId = $request->validated()['movie_id'];

        $request->validate([
            'include_image_language' => 'nullable|string',
            'language' => 'nullable|string',
        ]);

        $query = $this->buildQuery($request, ['include_image_language', 'language']);

        $result = $this->client->movieImages($movieId, $query);

        return $this->handleTmdb(fn() => $result, 'No images found for this movie.');
    }

    public function videos(MovieRequest $request): JsonResponse
    {
        $movieId = $request->validated()['movie_id'];

        $request->validate([
            'language' => 'nullable|string',
        ]);

        $query = $this->buildQuery($request, ['language']);

        $result = $this->client->movieVideos($movieId, $query);

        return $this->handleTmdb(fn() => $result, 'No videos found for this movie.');
    }

    public function recommendations(MovieRequest $request): JsonResponse
    {
        $movieId = $request->validated()['movie_id'];

        $request->validate([
            'language' => 'nullable|string',
            'page' => 'nullable|integer|min:-2147483648|max:2147483647',
        ]);

        $query = $this->buildQuery($request, ['language', 'page']);

        $result = $this->client->movieRecommendations($movieId, $query);

        return $this->handleTmdb(fn() => $result, 'No recommendations available for this movie.');
    }

    public function similar(MovieRequest $request): JsonResponse
    {
        $movieId = $request->validated()['movie_id'];

        $request->validate([
            'language' => 'nullable|string',
            'page' => 'nullable|integer|min:-2147483648|max:2147483647',
        ]);

        $query = $this->buildQuery($request, ['language', 'page']);

        $result = $this->client->similarMovies($movieId, $query);

        return $this->handleTmdb(fn() => $result, 'No similar movies found for this movie.');
    }
}
