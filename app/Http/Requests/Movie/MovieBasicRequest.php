<?php

namespace App\Http\Requests\Movie;

use App\Http\Requests\TmdbIdRequest;

class MovieBasicRequest extends TmdbIdRequest
{
    protected function idParam(): string
    {
        return 'movie_id';
    }
}
