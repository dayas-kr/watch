<?php

namespace App\Http\Controllers\Concerns;

use App\Services\TmdbResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

trait HandlesTmdbResult
{
  protected function tmdbError(
    TmdbResult $result,
    string     $notFoundMessage = 'Resource not found.',
    string     $context = '',
  ): ?JsonResponse {
    if ($result->succeeded()) {
      return null;
    }

    if ($result->isNotFound()) {
      return response()->json([
        'message' => $notFoundMessage,
      ], Response::HTTP_NOT_FOUND);
    }

    if ($result->isServerError()) {
      Log::error('TMDB server error' . ($context ? " [{$context}]" : ''), [
        'code'  => $result->statusCode(),
        'error' => $result->errorMessage(),
      ]);

      return response()->json([
        'message' => 'The service is temporarily unavailable. Please try again later.',
      ], Response::HTTP_SERVICE_UNAVAILABLE);
    }

    Log::error('TMDB unexpected error' . ($context ? " [{$context}]" : ''), [
      'label' => $result->errorLabel(),
      'code'  => $result->statusCode(),
      'error' => $result->errorMessage(),
    ]);

    return response()->json([
      'message' => 'Something went wrong.',
    ], Response::HTTP_INTERNAL_SERVER_ERROR);
  }
}
