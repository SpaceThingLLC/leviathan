<?php

declare(strict_types=1);

namespace Leviathan\Exceptions;

/**
 * Exception thrown when API quota is exceeded.
 */
class QuotaExceededException extends KrakenException
{
    /**
     * Create a new quota exceeded exception.
     */
    public static function quotaExceeded(): static
    {
        return new static(
            'Your Kraken.io API quota has been exceeded. Please upgrade your plan or wait until your quota resets.',
            429
        );
    }
}
