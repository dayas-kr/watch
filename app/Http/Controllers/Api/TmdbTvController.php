<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\HandlesTmdb;
use App\Http\Controllers\Controller;
use App\Http\Requests\Title\TitleListsRequest;
use App\Http\Requests\Tv\TvRequest;
use App\Services\TmdbClient;
use App\Support\Concerns\BuildsQuery;
use App\Support\TmdbAppend;
use Illuminate\Http\JsonResponse;

class TmdbTvController extends Controller
{
    use HandlesTmdb, BuildsQuery;

    public function __construct(protected TmdbClient $client) {}

    public function airingToday(TitleListsRequest $request): JsonResponse
    {
        $request->validate([
            'timezone' => 'nullable|string',
        ]);

        $query = $this->buildQuery($request, ['language', 'page', 'timezone']);

        $result = $this->client->airingTodayTv($query);

        return $this->handleTmdb(fn() => $result, 'No TV shows airing today for the given criteria.');
    }

    public function onTheAir(TitleListsRequest $request): JsonResponse
    {
        $request->validate([
            'timezone' => 'nullable|string',
        ]);

        $query = $this->buildQuery($request, ['language', 'page', 'timezone']);

        $result = $this->client->onTheAirTv($query);

        return $this->handleTmdb(fn() => $result, 'No TV shows currently on the air for the given criteria.');
    }

    public function popular(TitleListsRequest $request): JsonResponse
    {
        $query = $this->buildQuery($request, ['language', 'page']);

        $result = $this->client->popularTv($query);

        return $this->handleTmdb(fn() => $result, 'No popular TV shows found for the given criteria.');
    }

    public function topRated(TitleListsRequest $request): JsonResponse
    {
        $query = $this->buildQuery($request, ['language', 'page']);

        $result = $this->client->topRatedTv($query);

        return $this->handleTmdb(fn() => $result, 'No top-rated TV shows found for the given criteria.');
    }

    public function show(TvRequest $request): JsonResponse
    {
        $tv_id = $request->validated()['tv_id'];

        $request->validate(
            [
                'language' => 'nullable|string',
                'append_to_response' => [
                    'nullable',
                    'string',
                    'regex:' . TmdbAppend::regex(TmdbAppend::tv()),
                ],
            ],
            [
                'append_to_response.regex' => 'Invalid format. Use comma-separated values. Supported values: ' . implode(', ', TmdbAppend::tv()) . '.',
            ]
        );

        $query = $this->buildQuery($request, ['language', 'append_to_response']);

        $result = $this->client->tv($tv_id, $query);

        return $this->handleTmdb(fn() => $result, 'No TV show found. Please verify the provided TV show ID.');
    }

    public function credits(TvRequest $request): JsonResponse
    {
        $tv_id = $request->validated()['tv_id'];

        $request->validate([
            'language' => 'nullable|string',
        ]);

        $query = $this->buildQuery($request, ['language']);

        $result = $this->client->tvCredits($tv_id, $query);

        return $this->handleTmdb(fn() => $result, 'No credits found for this TV show.');
    }

    public function images(TvRequest $request): JsonResponse
    {
        $tv_id = $request->validated()['tv_id'];

        $request->validate([
            'include_image_language' => 'nullable|string',
            'language' => 'nullable|string',
        ]);

        $query = $this->buildQuery($request, ['include_image_language', 'language']);

        $result = $this->client->tvImages($tv_id, $query);

        return $this->handleTmdb(fn() => $result, 'No images found for this TV show.');
    }

    public function videos(TvRequest $request): JsonResponse
    {
        $tv_id = $request->validated()['tv_id'];

        $request->validate([
            'include_video_language' => 'nullable|string',
            'language' => 'nullable|string',
        ]);

        $query = $this->buildQuery($request, ['include_video_language', 'language']);

        $result = $this->client->tvVideos($tv_id, $query);

        return $this->handleTmdb(fn() => $result, 'No videos found for this TV show.');
    }

    public function recommendations(TvRequest $request): JsonResponse
    {
        $tv_id = $request->validated()['tv_id'];

        $request->validate([
            'language' => 'nullable|string',
            'page' => 'nullable|integer|min:-2147483648|max:2147483647',
        ]);

        $query = $this->buildQuery($request, ['language', 'page']);

        $result = $this->client->tvRecommendations($tv_id, $query);

        return $this->handleTmdb(fn() => $result, 'No recommendations available for this TV show.');
    }

    public function similar(TvRequest $request): JsonResponse
    {
        $tv_id = $request->validated()['tv_id'];

        $request->validate([
            'language' => 'nullable|string',
            'page' => 'nullable|integer|min:-2147483648|max:2147483647',
        ]);

        $query = $this->buildQuery($request, ['language', 'page']);

        $result = $this->client->similarTv($tv_id, $query);

        return $this->handleTmdb(fn() => $result, 'No similar TV shows found for this TV show.');
    }
}
