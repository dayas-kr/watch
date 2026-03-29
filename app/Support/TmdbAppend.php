<?php

namespace App\Support;

class TmdbAppend
{
    public static function movie(): array
    {
        return [
            "account_states",
            "alternative_titles",
            "changes",
            "credits",
            "external_ids",
            "images",
            "keywords",
            "lists",
            "recommendations",
            "release_dates",
            "reviews",
            "similar",
            "translations",
            "videos",
            "watch/providers",
        ];
    }

    public static function tv(): array
    {
        return [
            "account_states",
            "aggregate_credits",
            "alternative_titles",
            "changes",
            "content_ratings",
            "credits",
            "episode_groups",
            "external_ids",
            "images",
            "keywords",
            "lists",
            "recommendations",
            "reviews",
            "screened_theatrically",
            "similar",
            "translations",
            "videos",
            "watch/providers",
        ];
    }

    public static function regex(array $values): string
    {
        $escaped = array_map(fn($v) => preg_quote($v, '#'), $values);

        $pattern = implode('|', $escaped);

        return "#^({$pattern})(,({$pattern}))*$#";
    }
}
