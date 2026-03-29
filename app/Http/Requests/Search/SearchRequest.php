<?php

namespace App\Http\Requests\Search;

use App\Http\Requests\BaseApiRequest;

class SearchRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'query' => "required|string|min:1",
            'page' => "nullable|integer|min:-2147483648|max:2147483647",
        ];
    }
}
