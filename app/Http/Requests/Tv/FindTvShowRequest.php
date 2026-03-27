<?php

namespace App\Http\Requests\Tv;

use Illuminate\Foundation\Http\FormRequest;

class FindTvShowRequest extends FormRequest
{
    private const ALLOWED_APPENDS = ['aggregate_credits', 'videos', 'images', 'recommendations'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $allowed = implode('|', self::ALLOWED_APPENDS);

        return [
            'tv_id' => 'required|integer|min:-2147483648|max:2147483647',
            'append_to_response' => [
                'nullable',
                'string',
                'regex:/^(' . $allowed . ')(,(' . $allowed . '))*$/',
            ],
        ];
    }

    public function messages(): array
    {
        $supported = implode(', ', self::ALLOWED_APPENDS);

        return [
            'append_to_response.regex' =>
            "Invalid value for append_to_response. Supported values are: {$supported}.",
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tv_id' => $this->route('id'),
        ]);
    }
}
