<?php

namespace App\Http\Controllers\Concerns;

use App\Exceptions\TmdbRequestException;
use App\Http\Helpers\TmdbResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;

trait HandlesTmdb
{
    protected function handleTmdb(callable $callback, string $errorMessage): JsonResponse
    {
        try {
            return TmdbResponseHelper::handleSuccess($callback());
        } catch (TmdbRequestException $e) {
            return TmdbResponseHelper::handleException($e, $errorMessage);
        } catch (Throwable $e) {
            logger()->error($e);
            return TmdbResponseHelper::handleServerError();
        }
    }
}
