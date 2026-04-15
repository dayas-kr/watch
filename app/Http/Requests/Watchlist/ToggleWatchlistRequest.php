<?php

namespace App\Http\Requests\Watchlist;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;

class ToggleWatchlistRequest extends BaseApiRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'watchlist' => $this->watchlist ? true : false,
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ((int) $this->user_id !== Auth::id()) {
                $validator->errors()->add('user_id', 'User ID does not match the authenticated user.');
            }
        });
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
            'user_id.required' => 'User ID is required.',
        ];
    }
}
