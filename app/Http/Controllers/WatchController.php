<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseApiRequest;
use App\Models\ListType;
use App\Models\MediaType;
use App\Models\UserList;
use Illuminate\Support\Facades\Auth;

class WatchController extends Controller
{
    public function __invoke(BaseApiRequest $request)
    {
        $request->validate([
            'media_type' => 'required|string|in:movie,tv',
            'media_id'   => 'required|integer|min:-2147483648|max:2147483647',
            'watched'    => 'required|boolean',
        ]);

        $mediaType = MediaType::where('name', $request->media_type)->value('id');

        if (!$mediaType) {
            return response()->json(['success' => false, 'message' => 'Invalid media type.'], 422);
        }

        $list     = UserList::defaultOfType(Auth::id(), ListType::WATCHED);
        $criteria = ['media_id' => $request->media_id, 'media_type' => $mediaType];

        $request->watched
            ? $list->items()->firstOrCreate($criteria)
            : $list->items()->where($criteria)->delete();

        return response()->json(['success' => true, 'message' => 'Title synced successfully.']);
    }
}
