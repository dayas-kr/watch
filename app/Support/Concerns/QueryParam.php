<?php

namespace App\Support\Concerns;

use Illuminate\Http\Request;

trait QueryParam
{
    protected function buildQuery(Request $request, array $keys): array
    {
        $query = [];

        foreach ($keys as $key) {
            if ($request->filled($key)) {
                $query[$key] = $request->input($key);
            }
        }

        return $query;
    }
}
