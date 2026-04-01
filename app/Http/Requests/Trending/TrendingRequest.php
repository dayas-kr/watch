<?php

namespace App\Http\Requests\Trending;

use App\Http\Requests\BaseApiRequest;

class TrendingRequest extends BaseApiRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'time_window' => $this->route('time_window'),
        ]);
    }

    public function rules(): array
    {
        return [
            'time_window' => 'required|string|in:day,week',
            'language' => 'nullable|string'
        ];
    }

    public function messages(): array
    {
        return [
            'time_window.in' => 'Time window must be one of day or week.',
        ];
    }
}
