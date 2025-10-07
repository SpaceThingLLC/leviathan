<?php

declare(strict_types=1);

namespace Leviathan\Exceptions;

use Exception;

/**
 * Base exception for all Kraken.io related errors.
 */
class KrakenException extends Exception
{
    /**
     * Create a new Kraken exception instance.
     *
     * @param  array<string, mixed>|null  $response
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        protected ?array $response = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the raw API response if available.
     *
     * @return array<string, mixed>|null
     */
    public function getResponse(): ?array
    {
        return $this->response;
    }

    /**
     * Create an exception from an API error response.
     *
     * @param  array<string, mixed>  $response
     */
    public static function fromResponse(array $response): self
    {
        $message = is_string($response['message'] ?? null) ? $response['message'] : 'Unknown Kraken.io error';
        $code = is_int($response['code'] ?? null) ? $response['code'] : 0;

        return new self($message, $code, response: $response);
    }
}
