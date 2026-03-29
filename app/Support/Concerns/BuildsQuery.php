<?php

namespace App\Support\Concerns;

use Illuminate\Http\Request;

trait BuildsQuery
{
    protected function buildQuery(Request $request, array $keys): array
    {
        return array_filter(
            $request->only($keys),
            fn($value) => !is_null($value)
        );
    }
}
