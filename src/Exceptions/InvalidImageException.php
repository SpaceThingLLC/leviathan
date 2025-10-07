<?php

declare(strict_types=1);

namespace Leviathan\Exceptions;

/**
 * Exception thrown when an invalid image is provided.
 */
class InvalidImageException extends KrakenException
{
    /**
     * Create exception for invalid image URL.
     */
    public static function invalidUrl(string $url): self
    {
        return new self(
            "Invalid image URL: {$url}",
            400
        );
    }

    /**
     * Create exception for invalid image file.
     */
    public static function invalidFile(string $path): self
    {
        return new self(
            "Invalid or unreadable image file: {$path}",
            400
        );
    }

    /**
     * Create exception for unsupported image format.
     */
    public static function unsupportedFormat(string $format): self
    {
        return new self(
            "Unsupported image format: {$format}. Supported formats are: jpg, png, gif, webp, avif.",
            400
        );
    }
}
