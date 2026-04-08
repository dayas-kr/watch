<?php

namespace App\Http\Controllers;

use App\Models\ListType;
use App\Models\UserList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $watchlist = UserList::defaultOfType(Auth::id(), ListType::WATCHLIST)
            ->items()
            ->with('mediaType')
            ->pluck('media_id');

        $favorites = UserList::defaultOfType(Auth::id(), ListType::FAVORITES)
            ->items()
            ->with('mediaType')
            ->pluck('media_id');

        $data = [
            'watchlist' => $watchlist,
            'favorites' => $favorites
        ];

        return view('welcome', compact('data'));
    }
}
