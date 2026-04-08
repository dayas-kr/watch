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
        $data = [
            'watchlist' => $this->getList(ListType::WATCHLIST),
            'favorites' => $this->getList(ListType::FAVORITES),
            'watched' => $this->getList(ListType::WATCHED),
        ];

        return view('welcome', compact('data'));
    }

    private function getList($listType)
    {
        return UserList::defaultOfType(Auth::id(), $listType)
            ->items()
            ->with('mediaType')
            ->pluck('media_id');
    }
}
