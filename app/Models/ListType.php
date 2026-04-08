<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListType extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    const WATCHLIST = 1;
    const FAVORITES = 2;
    const WATCHED = 3;
}
