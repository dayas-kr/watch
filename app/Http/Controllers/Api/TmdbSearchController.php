<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\HandlesTmdb;
use App\Http\Controllers\Controller;
use App\Http\Requests\Search\SearchRequest;
use App\Services\TmdbClient;
use App\Support\Concerns\QueryParam;
use Illuminate\Http\JsonResponse;

class TmdbSearchController extends Controller
{
    use HandlesTmdb, QueryParam;

    public function __construct(protected TmdbClient $client) {}

    public function multi(SearchRequest $request): JsonResponse
    {
        $request->validate([
            'include_adult' => 'nullable|boolean',
            'language' => 'nullable|string',
        ]);

        $query = $this->buildQuery($request, [
            'query',
            'page',
            'include_adult',
            'language',
        ]);

        $result = $this->client->searchAllTitles($query);

        return $this->handleTmdb(fn() => $result, 'No results found for movies or TV shows matching your query.');
    }

    public function movie(SearchRequest $request): JsonResponse
    {
        $request->validate([
            'include_adult' => 'nullable|boolean',
            'language' => 'nullable|string',
            'primary_release_year' => 'nullable|string',
            'region' => 'nullable|string',
            'year' => 'nullable|string',
        ]);

        $query = $this->buildQuery($request, [
            'query',
            'page',
            'include_adult',
            'language',
            'primary_release_year',
            'region',
            'year',
        ]);

        $result = $this->client->searchMovies($query);

        return $this->handleTmdb(fn() => $result, 'No results found for movies matching your query.');
    }

    public function tv(SearchRequest $request): JsonResponse
    {
        $request->validate([
            'first_air_date_year' => 'nullable|integer|min:-2147483648|max:2147483647',
            'include_adult' => 'nullable|boolean',
            'language' => 'nullable|string',
            'year' => 'nullable|integer|min:-2147483648|max:2147483647',
        ]);

        $query = $this->buildQuery($request, [
            'query',
            'page',
            'first_air_date_year',
            'include_adult',
            'language',
            'year',
        ]);

        $result = $this->client->searchTv($query);

        return $this->handleTmdb(fn() => $result, 'No results found for TV shows matching your query.');
    }

    public function person(SearchRequest $request): JsonResponse
    {
        $request->validate([
            'include_adult' => 'nullable|boolean',
            'language' => 'nullable|string',
        ]);

        $query = $this->buildQuery($request, [
            'query',
            'page',
            'include_adult',
            'language',
        ]);

        $result = $this->client->searchPeople($query);

        return $this->handleTmdb(fn() => $result, 'No people found matching your query.');
    }

    public function collection(SearchRequest $request): JsonResponse
    {
        $request->validate([
            'include_adult' => 'nullable|boolean',
            'language' => 'nullable|string',
            'region' => 'nullable|string',
        ]);

        $query = $this->buildQuery($request, [
            'query',
            'page',
            'include_adult',
            'language',
            'region',
        ]);

        $result = $this->client->searchCollections($query);

        return $this->handleTmdb(fn() => $result, 'No collections found matching your query.');
    }

    public function company(SearchRequest $request): JsonResponse
    {
        $query = $this->buildQuery($request, ['query', 'page']);

        $result = $this->client->searchCompanies($query);

        return $this->handleTmdb(fn() => $result, 'No companies found matching your query.');
    }

    public function keyword(SearchRequest $request): JsonResponse
    {
        $query = $this->buildQuery($request, ['query', 'page']);

        $result = $this->client->searchKeywords($query);

        return $this->handleTmdb(fn() => $result, 'No keywords found matching your query.');
    }
}
