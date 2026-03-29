<?php

namespace App\Http\Requests\Movie;

use App\Http\Requests\BaseApiRequest;

class MovieRequest extends BaseApiRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'movie_id' => $this->route('movie_id'),
        ]);
    }

    public function rules(): array
    {
        return [
            'movie_id' => 'required|integer|min:-2147483648|max:2147483647',
        ];
    }

    public function messages(): array
    {
        return [
            'movie_id.required' => 'Movie ID is required.',
            'movie_id.integer' => 'Movie ID must be an integer.',
            'movie_id.min' => 'Movie ID must be greater than or equal to -2147483648.',
            'movie_id.max' => 'Movie ID must be less than or equal to 2147483647.',
        ];
    }
}
