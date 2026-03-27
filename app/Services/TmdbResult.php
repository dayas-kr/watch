<?php

namespace App\Services;

use App\Exceptions\TmdbRequestException;

/**
 * Wraps every TMDB API call so callers always receive a typed result
 * instead of bare arrays or swallowed exceptions.
 *
 * Usage:
 *   $result = $movies->getMovie(550);
 *
 *   if ($result->failed()) {
 *       // $result->errorMessage()  – human-readable message
 *       // $result->statusCode()    – HTTP status (0 = connection error)
 *       // $result->isNotFound()    – 404
 *       // $result->isServerError() – 5xx
 *   }
 *
 *   $data = $result->data();   // array  (empty when failed)
 *   $data = $result->get('title', 'Unknown');  // dot-notation helper
 */
final class TmdbResult
{
  private function __construct(
    private readonly array                $data,
    private readonly bool                 $ok,
    private readonly ?string              $errorMessage,
    private readonly int                  $statusCode,
    private readonly ?TmdbRequestException $exception,
  ) {}

  // -------------------------------------------------------
  // Named constructors
  // -------------------------------------------------------

  public static function ok(array $data): self
  {
    return new self(
      data: $data,
      ok: true,
      errorMessage: null,
      statusCode: 200,
      exception: null,
    );
  }

  public static function fail(TmdbRequestException $e): self
  {
    return new self(
      data: [],
      ok: false,
      errorMessage: $e->getMessage(),
      statusCode: $e->statusCode,
      exception: $e,
    );
  }

  // -------------------------------------------------------
  // Status checks
  // -------------------------------------------------------

  public function succeeded(): bool
  {
    return $this->ok;
  }

  public function failed(): bool
  {
    return !$this->ok;
  }

  /** HTTP 404 from TMDB (resource does not exist). */
  public function isNotFound(): bool
  {
    return $this->statusCode === 404;
  }

  /** HTTP 5xx from TMDB, or 0 for a connection / timeout failure. */
  public function isServerError(): bool
  {
    return $this->statusCode === 0 || $this->statusCode >= 500;
  }

  /** HTTP 401 / 403 – bad API key or missing permission. */
  public function isAuthError(): bool
  {
    return in_array($this->statusCode, [401, 403], true);
  }

  /** Any non-5xx, non-404, non-auth client error (4xx). */
  public function isClientError(): bool
  {
    return $this->statusCode >= 400
      && !$this->isNotFound()
      && !$this->isAuthError();
  }

    // -------------------------------------------------------
    // Payload access
    // -------------------------------------------------------

  /** Raw response array. Empty array when the call failed. */
  public function data(): array
  {
    return $this->data;
  }

  /**
   * Dot-notation key access with an optional default.
   *
   * @param  mixed $default
   * @return mixed
   */
  public function get(string $key, mixed $default = null): mixed
  {
    return data_get($this->data, $key, $default);
  }

  // -------------------------------------------------------
  // Error details
  // -------------------------------------------------------

  public function errorMessage(): ?string
  {
    return $this->errorMessage;
  }

  public function statusCode(): int
  {
    return $this->statusCode;
  }

  public function exception(): ?TmdbRequestException
  {
    return $this->exception;
  }

  /**
   * Human-readable label for the failure reason.
   * Useful for logging or user-facing messages.
   */
  public function errorLabel(): string
  {
    if ($this->ok) {
      return 'ok';
    }

    return match (true) {
      $this->isNotFound()    => 'not_found',
      $this->isServerError() => 'server_error',
      $this->isAuthError()   => 'auth_error',
      $this->isClientError() => 'client_error',
      default                => 'unknown_error',
    };
  }
}
