<?php

namespace App\Http\Helpers;

use App\Exceptions\TmdbRequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TmdbResponseHelper
{
    public static function handleException(TmdbRequestException $e, string $notFoundMessage = 'Resource not found.'): JsonResponse
    {
        return match ($e->statusCode) {
            404     => response()->json(['message' => $notFoundMessage], Response::HTTP_NOT_FOUND),
            503     => response()->json(['message' => 'Service temporarily unavailable. Please try again later.'], Response::HTTP_SERVICE_UNAVAILABLE),
            504,
            505     => response()->json(['message' => 'Gateway timeout. TMDB is not responding.'], Response::HTTP_GATEWAY_TIMEOUT),
            default => response()->json(['message' => $e->getMessage() ?: 'TMDB error.'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }

    public static function handleServerError(): JsonResponse
    {
        return response()->json(['message' => 'Server error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public static function handleSuccess($data): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $data], Response::HTTP_OK);
    }
}
