<?php

namespace BlaiseBueno\LaravelDomainToolkit\Support;

use Illuminate\Http\JsonResponse;

/**
 * Standardized wrapper for service layer return.
 * Encapsulates data, errors, and HTTP status handling.
 */
class ServiceReturn
{
    public function __construct(
        public readonly mixed $data = null, 
        public readonly ?array $errors = null, 
        public readonly int $statusCode = 200
    ) {}

    public function isSuccess(): bool
    {
        return empty($this->errors);
    }

    public static function success(mixed $data = null, int $statusCode = 200): self
    {
        return new self($data, null, $statusCode);
    }

    private static function normalizeErrors(array|string|null $errors): array
    {
        if (empty($errors)) {
            return ['message' => 'An error occurred'];
        }

        if (is_string($errors)) {
            return ['message' => $errors];
        }

        return $errors;
    }

    public static function clientError(array|string|null $errors = null, int $statusCode = 422): self
    {
        return new self(
            null,
            self::normalizeErrors($errors),
            $statusCode
        );
    }

    public static function serverError(array|string|null $errors = null, int $statusCode = 500): self
    {
        return new self(
            null,
            self::normalizeErrors($errors),
            $statusCode
        );
    }

    public function toArray(bool $withStatusCode = false): array
    {
        $data = [
            'data' => $this->data,
            'errors' => $this->errors,
        ];

        if($withStatusCode) {
            $data['status_code'] = $this->statusCode;
        }

        return $data;
    }

    public function toJsonResponse(bool $dataOnly = false, bool $withStatusCode = true,): JsonResponse
    {
        $data = $this->toArray($withStatusCode);
        // optional return data only
        if(!$data['errors'] && $dataOnly) {
            $data = $data['data'];
        }

        return response()->json($data, $this->statusCode);
    }
}
