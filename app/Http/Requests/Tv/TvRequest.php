<?php

namespace App\Http\Requests\Tv;

use App\Http\Requests\BaseApiRequest;

class TvRequest extends BaseApiRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'tv_id' => $this->route('tv_id'),
        ]);
    }

    public function rules(): array
    {
        return [
            'tv_id' => 'required|integer|min:-2147483648|max:2147483647',
        ];
    }

    public function messages(): array
    {
        return [
            'tv_id.required' => 'TV show ID is required.',
            'tv_id.integer' => 'TV show ID must be an integer.',
            'tv_id.min' => 'TV show ID must be greater than or equal to -2147483648.',
            'tv_id.max' => 'TV show ID must be less than or equal to 2147483647.',
        ];
    }
}
