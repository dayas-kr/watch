<?php

namespace App\Models;

use App\Models\MediaType;
use App\Models\UserList;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListItem extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'added_at' => 'datetime',
    ];

    public function list(): BelongsTo
    {
        return $this->belongsTo(UserList::class, 'list_id');
    }

    public function mediaType(): BelongsTo
    {
        return $this->belongsTo(MediaType::class, 'media_type');
    }
}
