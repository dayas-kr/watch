<?php

namespace App\Http\Requests;

use App\Http\Helpers\TmdbValidationRules;
use Illuminate\Foundation\Http\FormRequest;

abstract class TmdbIdRequest extends FormRequest
{
  abstract protected function idParam(): string;

  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      $this->idParam() => TmdbValidationRules::id(),
    ];
  }

  protected function prepareForValidation(): void
  {
    $this->merge([
      $this->idParam() => $this->route('id'),
    ]);
  }
}
