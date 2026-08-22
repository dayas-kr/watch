<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesTmdb;
use App\Http\Requests\BaseApiRequest;
use App\Models\ListItem;
use App\Models\ListType;
use App\Models\MediaType;
use App\Models\UserList;
use App\Services\TmdbClient;
use App\Support\Concerns\BuildsQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TmdbListController extends Controller
{
    use HandlesTmdb, BuildsQuery;

    public function __construct(protected TmdbClient $client) {}

    public function index(): JsonResponse
    {
        return $this->handleTmdb(fn() => $this->client->lists());
    }

    public function show(BaseApiRequest $request, int $list_id): JsonResponse
    {
        $request->validate([
            'page' => "nullable|integer|min:-2147483648|max:2147483647",
        ]);

        return $this->handleTmdb(fn() => $this->client->list($list_id, $this->buildQuery($request, ['page'])));
    }

    public function store(BaseApiRequest $request): JsonResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'iso_639_1'   => ['required', 'string', 'size:2'],
            'description' => ['nullable', 'string'],
            'public'      => ['nullable', 'boolean'],
        ]);

        $query = $this->buildQuery($request, ['name', 'description', 'iso_639_1', 'public']);

        // 1. TMDB first — if this throws, DB is never touched
        $tmdbResponse = $this->handleTmdb(
            fn() => $this->client->createList($query),
            'Failed to create list.'
        );

        if ($tmdbResponse->getStatusCode() !== 200) {
            return $tmdbResponse;
        }

        // 2. DB sync — only reached if TMDB succeeded
        $tmdbListId = $tmdbResponse->getData(true)['data']['id'] ?? null;

        $list = UserList::create([
            'id'         => $tmdbListId,
            'user_id'    => Auth::id(),
            'list_type'  => ListType::CUSTOM,
            'name'       => $request->name,
            'is_public'  => $request->boolean('public'),
            'is_default' => false,
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'id' => $list->id,
                'description' => $request->description,
                'iso_639_1' => $request->iso_639_1,
                'name' => $request->name,
                'public' => $request->boolean('public'),
            ],
            'message' => 'List created successfully.',
        ]);
    }

    public function destroy(int $list_id): JsonResponse
    {
        // 1. TMDB first
        $tmdbResponse = $this->handleTmdb(
            fn() => $this->client->deleteList($list_id),
            'Failed to delete list.'
        );

        if ($tmdbResponse->getStatusCode() !== 200) {
            return $tmdbResponse;
        }

        // 2. DB sync
        UserList::where('id', $list_id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['success' => true, 'message' => 'List deleted successfully.']);
    }

    public function clear(int $list_id): JsonResponse
    {
        // 1. TMDB first
        $tmdbResponse = $this->handleTmdb(
            fn() => $this->client->clearList($list_id),
            'Failed to clear list.'
        );

        if ($tmdbResponse->getStatusCode() !== 200) {
            return $tmdbResponse;
        }

        // 2. DB sync
        ListItem::where('list_id', $list_id)->delete();

        return response()->json(['success' => true, 'message' => 'List cleared successfully.']);
    }

    public function addItems(BaseApiRequest $request, int $list_id): JsonResponse
    {
        $request->validate([
            'items'              => ['required', 'array', 'min:1'],
            'items.*.media_type' => ['required', 'string', 'in:movie,tv'],
            'items.*.media_id'   => ['required', 'integer'],
        ]);

        // 1. TMDB first
        $tmdbResponse = $this->handleTmdb(
            fn() => $this->client->addListItems($list_id, $request->items),
            'Failed to add items to list.'
        );

        if ($tmdbResponse->getStatusCode() !== 200) {
            return $tmdbResponse;
        }

        // 2. DB sync
        $mediaTypeMap = ['movie' => MediaType::MOVIE, 'tv' => MediaType::TV];

        $rows = array_map(fn($item) => [
            'list_id'    => $list_id,
            'media_id'   => $item['media_id'],
            'media_type' => $mediaTypeMap[$item['media_type']],
            'added_at'   => now(),
        ], $request->items);

        ListItem::upsert($rows, ['list_id', 'media_id', 'media_type']);

        return response()->json(['success' => true, 'message' => 'Items added successfully.']);
    }

    public function removeItems(BaseApiRequest $request, int $list_id): JsonResponse
    {
        $request->validate([
            'items'              => ['required', 'array', 'min:1'],
            'items.*.media_type' => ['required', 'string', 'in:movie,tv'],
            'items.*.media_id'   => ['required', 'integer'],
        ]);

        // 1. TMDB first
        $tmdbResponse = $this->handleTmdb(
            fn() => $this->client->removeListItems($list_id, $request->items),
            'Failed to remove items from list.'
        );

        if ($tmdbResponse->getStatusCode() !== 200) {
            return $tmdbResponse;
        }

        // 2. DB sync
        $mediaTypeMap = ['movie' => MediaType::MOVIE, 'tv' => MediaType::TV];

        foreach ($request->items as $item) {
            ListItem::where([
                'list_id'    => $list_id,
                'media_id'   => $item['media_id'],
                'media_type' => $mediaTypeMap[$item['media_type']],
            ])->delete();
        }

        return response()->json(['success' => true, 'message' => 'Items removed successfully.']);
    }
}
