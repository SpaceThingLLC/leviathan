<?php

declare(strict_types=1);

namespace Leviathan\Client\Contracts;

/**
 * Interface for Kraken.io API client.
 */
interface KrakenClientInterface
{
    /**
     * Optimize an image from a URL.
     *
     * @param  string  $url  The URL of the image to optimize
     * @param  array<string, mixed>  $options  Optimization options
     * @return array<string, mixed> The API response
     *
     * @throws \Leviathan\Exceptions\KrakenException
     */
    public function optimizeUrl(string $url, array $options = []): array;

    /**
     * Upload and optimize an image file.
     *
     * @param  string  $filePath  Path to the image file
     * @param  array<string, mixed>  $options  Optimization options
     * @return array<string, mixed> The API response
     *
     * @throws \Leviathan\Exceptions\KrakenException
     */
    public function uploadFile(string $filePath, array $options = []): array;

    /**
     * Get user account data and quota information.
     *
     * @return array<string, mixed> The API response
     *
     * @throws \Leviathan\Exceptions\KrakenException
     */
    public function getUserData(): array;
}
