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
     */
    public function getResponse(): ?array
    {
        return $this->response;
    }

    /**
     * Create an exception from an API error response.
     */
    public static function fromResponse(array $response): static
    {
        $message = $response['message'] ?? 'Unknown Kraken.io error';
        $code = $response['code'] ?? 0;

        return new static($message, $code, response: $response);
    }
}
