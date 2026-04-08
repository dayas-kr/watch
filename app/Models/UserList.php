<?php

namespace App\Models;

use App\Models\ListItem;
use App\Models\ListType;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class UserList extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_default' => 'boolean',
        'is_public'  => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function listType(): BelongsTo
    {
        return $this->belongsTo(ListType::class, 'list_type');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ListItem::class, 'list_id');
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    public function scopeOfType(Builder $query, int $type): Builder
    {
        return $query->where('list_type', $type);
    }

    public static function defaultOfType(int $userId, int $type): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId, 'list_type' => $type, 'is_default' => true],
            ['name' => ListType::find($type)->name, 'is_public' => false]
        );
    }
}
