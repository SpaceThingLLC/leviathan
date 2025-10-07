<?php

declare(strict_types=1);

namespace Leviathan\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Leviathan\Client\Contracts\KrakenClientInterface;
use Leviathan\Exceptions\AuthenticationException;
use Leviathan\Exceptions\InvalidImageException;
use Leviathan\Exceptions\KrakenException;
use Leviathan\Exceptions\QuotaExceededException;

/**
 * Kraken.io API client implementation.
 */
class KrakenClient implements KrakenClientInterface
{
    private Client $httpClient;

    /**
     * Create a new Kraken client instance.
     */
    public function __construct(
        private readonly string $apiKey,
        private readonly string $apiSecret,
        private readonly string $apiUrl = 'https://api.kraken.io/v1',
        private readonly int $timeout = 30,
        private readonly int $retryAttempts = 3
    ) {
        if (empty($this->apiKey) || empty($this->apiSecret)) {
            throw AuthenticationException::missingCredentials();
        }

        $this->httpClient = new Client([
            'base_uri' => $this->apiUrl,
            'timeout' => $this->timeout,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function optimizeUrl(string $url, array $options = []): array
    {
        if (empty($url) || ! filter_var($url, FILTER_VALIDATE_URL)) {
            throw InvalidImageException::invalidUrl($url);
        }

        $payload = $this->buildPayload([
            'url' => $url,
            ...$options,
        ]);

        return $this->makeRequest('POST', '/url', ['json' => $payload]);
    }

    /**
     * {@inheritDoc}
     */
    public function uploadFile(string $filePath, array $options = []): array
    {
        if (! file_exists($filePath) || ! is_readable($filePath)) {
            throw InvalidImageException::invalidFile($filePath);
        }

        $payload = $this->buildPayload($options);

        return $this->makeRequest('POST', '/upload', [
            'multipart' => [
                [
                    'name' => 'data',
                    'contents' => json_encode($payload),
                ],
                [
                    'name' => 'file',
                    'contents' => fopen($filePath, 'r'),
                    'filename' => basename($filePath),
                ],
            ],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getUserData(): array
    {
        $payload = [
            'auth' => [
                'api_key' => $this->apiKey,
                'api_secret' => $this->apiSecret,
            ],
        ];

        return $this->makeRequest('POST', '/userdata', ['json' => $payload]);
    }

    /**
     * Build the request payload with authentication.
     *
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    private function buildPayload(array $options): array
    {
        return [
            'auth' => [
                'api_key' => $this->apiKey,
                'api_secret' => $this->apiSecret,
            ],
            ...$options,
        ];
    }

    /**
     * Make an HTTP request to the Kraken API.
     *
     * @param  string  $method  HTTP method
     * @param  string  $endpoint  API endpoint
     * @param  array<string, mixed>  $options  Request options
     * @return array<string, mixed> Decoded response
     *
     * @throws KrakenException
     */
    private function makeRequest(string $method, string $endpoint, array $options = []): array
    {
        $attempt = 0;

        while ($attempt < $this->retryAttempts) {
            try {
                $response = $this->httpClient->request($method, $endpoint, $options);
                $body = (string) $response->getBody();
                $data = json_decode($body, true);

                if (json_last_error() !== JSON_ERROR_NONE || ! is_array($data)) {
                    throw new KrakenException('Invalid JSON response from Kraken.io API');
                }

                // Check for API errors in response
                if (isset($data['success']) && $data['success'] === false) {
                    return $this->handleApiError($data);
                }

                return $data;
            } catch (GuzzleException $e) {
                $attempt++;

                if ($attempt >= $this->retryAttempts) {
                    throw new KrakenException(
                        "Failed to connect to Kraken.io API after {$this->retryAttempts} attempts: {$e->getMessage()}",
                        $e->getCode(),
                        $e
                    );
                }

                // Wait before retrying (exponential backoff)
                usleep(pow(2, $attempt) * 100000); // 0.2s, 0.4s, 0.8s
            }
        }

        throw new KrakenException('Unexpected error during API request');
    }

    /**
     * Handle API error responses.
     *
     * @param  array<string, mixed>  $response
     *
     * @throws KrakenException
     */
    private function handleApiError(array $response): never
    {
        $message = is_string($response['message'] ?? null) ? $response['message'] : 'Unknown error';
        $error = is_string($response['error'] ?? null) ? $response['error'] : '';

        // Map specific errors to custom exceptions
        if (str_contains($error, 'Authentication') || str_contains($message, 'Unauthorized')) {
            throw AuthenticationException::invalidCredentials();
        }

        if (str_contains($error, 'quota') || str_contains($message, 'quota')) {
            throw QuotaExceededException::quotaExceeded();
        }

        throw KrakenException::fromResponse($response);
    }
}
