<?php

namespace App\Exceptions;

class TmdbRequestException extends \RuntimeException
{
  public function __construct(
    string $message,
    public readonly int $statusCode,
    ?\Throwable $previous = null,
  ) {
    parent::__construct($message, $statusCode, $previous);
  }
}
