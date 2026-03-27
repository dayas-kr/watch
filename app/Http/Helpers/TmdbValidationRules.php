<?php

namespace App\Http\Helpers;

class TmdbValidationRules
{
    private const ALLOWED_APPENDS = ['credits', 'videos', 'images', 'recommendations'];

    public static function id(): array
    {
        return ['required', 'integer', 'min:-2147483648', 'max:2147483647'];
    }

    public static function appendToResponse(): array
    {
        $allowed = implode('|', self::ALLOWED_APPENDS);

        return [
            'nullable',
            'string',
            'regex:/^(' . $allowed . ')(,(' . $allowed . '))*$/',
        ];
    }

    public static function appendToResponseMessage(): string
    {
        return 'Invalid value for append_to_response. Supported values are: ' . implode(', ', self::ALLOWED_APPENDS) . '.';
    }
}
