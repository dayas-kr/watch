<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaType extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    const MOVIE = 1;
    const TV    = 2;
}
