<?php

namespace App\Http\Requests\Tv;

use App\Http\Requests\TmdbIdRequest;

class TvBasicRequest extends TmdbIdRequest
{
  protected function idParam(): string
  {
    return 'tv_id';
  }
}
