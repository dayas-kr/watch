<?php

namespace App\Http\Requests\Watchlist;

use App\Http\Requests\BaseApiRequest;

class ToggleWatchlistRequest extends BaseApiRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'watchlist' => $this->watchlist ? true : false,
        ]);
    }

    public function rules(): array
    {
        return [
            'media_type' => 'required|string|in:movie,tv',
            'media_id' => 'required|integer|min:-2147483648|max:2147483647',
            'watchlist' => 'required|boolean'
        ];
    }

    public function messages()
    {
        return [
            'media_type.in' => 'Media type must be one of movie or tv.',
        ];
    }
}
