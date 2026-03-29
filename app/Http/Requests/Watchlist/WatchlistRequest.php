<?php

namespace App\Http\Requests\Watchlist;

use App\Http\Requests\BaseApiRequest;

class WatchlistRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'language' => 'nullable|string',
            'page' => 'nullable|integer|min:1',
            'session_id' => 'nullable|string',
            'sort_by' => 'nullable|string|in:created_at.asc,created_at.desc',
        ];
    }

    public function messages()
    {
        return [
            'sort_by.in' => 'Sort by must be one of created_at.asc or created_at.desc',
        ];
    }
}
