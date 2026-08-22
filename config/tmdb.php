<?php

return [
  'base_url' => env('TMDB_URL', 'https://api.themoviedb.org/3'),
  'base_url_v4' => env('TMDB_URL_V4', 'https://api.themoviedb.org/4'),
  'api_key' => env('TMDB_API_KEY'),
  'account_id' => env('TMDB_ACCOUNT_ID'),
  'access_token' => env('TMDB_API_ACCESS_TOKEN'),
  'user_access_token' => env('TMDB_USER_ACCESS_TOKEN'),
];
