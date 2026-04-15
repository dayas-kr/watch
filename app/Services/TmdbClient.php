<?php

namespace App\Services;

use App\Exceptions\TmdbConfigurationException;
use App\Exceptions\TmdbRequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TmdbClient
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $accessToken;
    protected string $accountId;

    public function __construct()
    {
        $this->baseUrl     = $this->requireConfig('tmdb.base_url');
        $this->apiKey      = $this->requireConfig('tmdb.api_key');
        $this->accessToken = $this->requireConfig('tmdb.access_token');
        $this->accountId   = $this->requireConfig('tmdb.account_id');
    }

    // -------------------------------------------------------
    // SEARCH
    // -------------------------------------------------------

    public function searchAllTitles(array $query = []): array
    {
        return $this->get('/search/multi', $query);
    }

    public function searchMovies(array $query = []): array
    {
        return $this->get('/search/movie', $query);
    }

    public function searchTv(array $query = []): array
    {
        return $this->get('/search/tv', $query);
    }

    public function searchCollections(array $query = []): array
    {
        return $this->get('/search/collection', $query);
    }

    public function searchCompanies(array $query = []): array
    {
        return $this->get('/search/company', $query);
    }

    public function searchKeywords(array $query = []): array
    {
        return $this->get('/search/keyword', $query);
    }

    public function searchPeople(array $query = []): array
    {
        return $this->get('/search/person', $query);
    }

    // -------------------------------------------------------
    // WATCHLIST
    // -------------------------------------------------------

    public function movieWatchlist(array $query = []): array
    {
        return $this->getV4("/account/{$this->accountId}/watchlist/movies", $query);
    }

    public function tvWatchlist(array $query = []): array
    {
        return $this->getV4("/account/{$this->accountId}/watchlist/tv", $query);
    }

    public function toggleWatchlist(array $query = []): array
    {
        return $this->post("/account/{$this->accountId}/watchlist", $query);
    }

    // -------------------------------------------------------
    // LIST
    // -------------------------------------------------------

    public function lists(array $query = []): array
    {
        return $this->getV4("/account/{$this->accountId}/lists", $query);
    }

    public function list(int|string $list_id, array $query = []): array
    {
        return $this->getV4("/list/{$list_id}", $query);
    }

    public function createList(array $data = []): array
    {
        return $this->postV4('/list', $data);
    }

    public function clearList(int|string $list_id): array
    {
        return $this->postV4("/list/{$list_id}/clear");
    }

    public function deleteList(int|string $list_id): array
    {
        return $this->deleteV4("/list/{$list_id}");
    }

    public function addListItems(int|string $list_id, array $items): array
    {
        return $this->postV4("/list/{$list_id}/items", ['items' => $items]);
    }

    public function removeListItems(int|string $list_id, array $items): array
    {
        return $this->deleteV4("/list/{$list_id}/items", ['items' => $items]);
    }

    public function listItemStatus(int|string $list_id, int $media_id, string $media_type): array
    {
        return $this->getV4("/list/{$list_id}/item_status", [
            'media_id'   => $media_id,
            'media_type' => $media_type,
        ]);
    }

    // -------------------------------------------------------
    // TRENDING
    // -------------------------------------------------------

    public function trendingTitles(string $timeWindow, array $query = []): array
    {
        return $this->get("/trending/all/{$timeWindow}", $query);
    }

    public function trendingMovies(string $timeWindow, array $query = []): array
    {
        return $this->get("/trending/movie/{$timeWindow}", $query);
    }

    public function trendingTv(string $timeWindow, array $query = []): array
    {
        return $this->get("/trending/tv/{$timeWindow}", $query);
    }

    public function trendingPeople(string $timeWindow, array $query = []): array
    {
        return $this->get("/trending/person/{$timeWindow}", $query);
    }

    // -------------------------------------------------------
    // MOVIES
    // -------------------------------------------------------

    public function movie(int|string $id, array $query = []): array
    {
        return $this->get("/movie/{$id}", $query);
    }

    public function movieCredits(int|string $id, array $query = []): array
    {
        return $this->get("/movie/{$id}/credits", $query);
    }

    public function movieImages(int|string $id, array $query = []): array
    {
        return $this->get("/movie/{$id}/images", $query);
    }

    public function movieVideos(int|string $id, array $query = []): array
    {
        return $this->get("/movie/{$id}/videos", $query);
    }

    public function movieRecommendations(int|string $id, array $query = []): array
    {
        return $this->get("/movie/{$id}/recommendations", $query);
    }

    public function similarMovies(int|string $id, array $query = []): array
    {
        return $this->get("/movie/{$id}/similar", $query);
    }

    public function nowPlayingMovies(array $query = []): array
    {
        return $this->get("/movie/now_playing", $query);
    }

    public function popularMovies(array $query = []): array
    {
        return $this->get("/movie/popular", $query);
    }

    public function topRatedMovies(array $query = []): array
    {
        return $this->get("/movie/top_rated", $query);
    }

    public function upcomingMovies(array $query = []): array
    {
        return $this->get("/movie/upcoming", $query);
    }

    // -------------------------------------------------------
    // TV
    // -------------------------------------------------------

    public function tv(int|string $id, array $query = []): array
    {
        return $this->get("/tv/{$id}", $query);
    }

    public function tvCredits(int|string $id, array $query = []): array
    {
        return $this->get("/tv/{$id}/aggregate_credits", $query);
    }

    public function tvImages(int|string $id, array $query = []): array
    {
        return $this->get("/tv/{$id}/images", $query);
    }

    public function tvVideos(int|string $id, array $query = []): array
    {
        return $this->get("/tv/{$id}/videos", $query);
    }

    public function tvRecommendations(int|string $id, array $query = []): array
    {
        return $this->get("/tv/{$id}/recommendations", $query);
    }

    public function similarTv(int|string $id, array $query = []): array
    {
        return $this->get("/tv/{$id}/similar", $query);
    }

    public function airingTodayTv(array $query = []): array
    {
        return $this->get("/tv/airing_today", $query);
    }

    public function onTheAirTv(array $query = []): array
    {
        return $this->get("/tv/on_the_air", $query);
    }

    public function popularTv(array $query = []): array
    {
        return $this->get("/tv/popular", $query);
    }

    public function topRatedTv(array $query = []): array
    {
        return $this->get("/tv/top_rated", $query);
    }

    // -------------------------------------------------------
    // CORE REQUEST METHOD
    // -------------------------------------------------------

    public function get(string $endpoint, array $query = []): array
    {
        try {
            $response = $this->v3()->get($endpoint, $query);

            $data = $response->json();

            // TMDB "not found"
            if (isset($data['status_code']) && $data['status_code'] == 34) {
                throw new TmdbRequestException(
                    message: 'Resource not found.',
                    statusCode: 404
                );
            }

            return $data;
        } catch (ConnectionException $e) {
            $this->handleConnectionException($endpoint, $e);
        }
    }

    public function getV4(string $endpoint, array $query = []): array
    {
        try {
            $response = $this->v4()->get($endpoint, $query);

            return $response->json();
        } catch (ConnectionException $e) {
            $this->handleConnectionException($endpoint, $e);
        }
    }

    public function postV4(string $endpoint, array $data = []): array
    {
        try {
            $response = $this->v4()->post($endpoint, $data);

            return $response->json();
        } catch (ConnectionException $e) {
            $this->handleConnectionException($endpoint, $e);
        }
    }

    public function deleteV4(string $endpoint, array $data = []): array
    {
        try {
            $response = $this->v4()->delete($endpoint, $data);

            return $response->json();
        } catch (ConnectionException $e) {
            $this->handleConnectionException($endpoint, $e);
        }
    }

    public function post(string $endpoint, array $data = []): array
    {
        try {
            $response = $this->v3()->post($endpoint, $data);

            return $response->json();
        } catch (ConnectionException $e) {
            $this->handleConnectionException($endpoint, $e);
        }
    }

    // -------------------------------------------------------
    // CLIENTS
    // -------------------------------------------------------

    public function v3(): PendingRequest
    {
        return $this->base()->withQueryParameters([
            'api_key'  => $this->apiKey,
            'language' => 'en-US',
        ]);
    }

    public function v4(): PendingRequest
    {
        return $this->base()->withToken($this->accessToken);
    }

    protected function base(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->timeout(10)
            ->retry(3, fn($attempt) => $attempt * 200)
            ->withToken($this->accessToken)
            ->throw(function (Response $response, RequestException $e) {
                $this->handleRequestException($response, $e);
            });
    }

    // -------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------

    public function image(?string $path, string $size = 'w500'): ?string
    {
        if (!$path) return null;

        return "https://image.tmdb.org/t/p/{$size}{$path}";
    }

    // -------------------------------------------------------
    // ERROR HANDLING
    // -------------------------------------------------------

    protected function requireConfig(string $key): string
    {
        $value = config($key);

        if (blank($value)) {
            throw new TmdbConfigurationException("Missing TMDB config: [{$key}]");
        }

        return $value;
    }

    protected function handleRequestException(Response $response, RequestException $e): void
    {
        $status = $response->status();

        $message = $response->json('status_message')
            ?? $response->body()
            ?? 'Unknown error';

        Log::error('TMDB API error', [
            'status'  => $status,
            'message' => $message,
            'url'     => $this->scrubUrl($response->effectiveUri()?->__toString()),
            'method'  => request()->method() ?? 'unknown',
        ]);

        throw new TmdbRequestException(
            message: "TMDB request failed [{$status}]: {$message}",
            statusCode: $status,
            previous: $e
        );
    }

    protected function handleConnectionException(string $endpoint, ConnectionException $e): never
    {
        $safe = $this->scrubMessage($e->getMessage());

        Log::error('TMDB connection error', [
            'endpoint' => $endpoint,
            'error'    => $safe,
        ]);

        throw new TmdbRequestException(
            message: "TMDB connection failed: {$safe}",
            statusCode: 0,
            previous: $e
        );
    }

    protected function scrubUrl(?string $url = null): string
    {
        if (blank($url)) return '[unknown]';

        return preg_replace('/([?&])api_key=[^&]+(&|$)/', '$1', $url);
    }

    protected function scrubMessage(?string $message = null): string
    {
        if (blank($message)) return '[unknown]';

        return preg_replace('/api_key=[^&\s]+/i', 'api_key=[REDACTED]', $message);
    }
}
