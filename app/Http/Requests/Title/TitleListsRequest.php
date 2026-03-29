<?php

namespace App\Http\Requests\Title;

use App\Http\Requests\BaseApiRequest;

class TitleListsRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'language' => 'nullable|string',
            'page' => 'nullable|integer|min:-2147483648|max:2147483647',
        ];
    }
}
