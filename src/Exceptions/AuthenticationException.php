<?php

declare(strict_types=1);

namespace Leviathan\Exceptions;

/**
 * Exception thrown when API authentication fails.
 */
class AuthenticationException extends KrakenException
{
    /**
     * Create a new authentication exception.
     */
    public static function invalidCredentials(): self
    {
        return new self(
            'Invalid Kraken.io API credentials. Please check your API key and secret.',
            401
        );
    }

    /**
     * Create exception for missing credentials.
     */
    public static function missingCredentials(): self
    {
        return new self(
            'Kraken.io API credentials are not configured. Please set KRAKEN_API_KEY and KRAKEN_API_SECRET.',
            401
        );
    }
}
