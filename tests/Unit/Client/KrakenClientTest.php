<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Leviathan\Client\KrakenClient;
use Leviathan\Exceptions\AuthenticationException;
use Leviathan\Exceptions\InvalidImageException;
use Leviathan\Exceptions\KrakenException;

beforeEach(function () {
    // Store original client creation method
    $this->apiKey = 'test-api-key';
    $this->apiSecret = 'test-api-secret';
});

test('throws exception when api key is missing', function () {
    new KrakenClient('', 'secret');
})->throws(AuthenticationException::class, 'not configured');

test('throws exception when api secret is missing', function () {
    new KrakenClient('key', '');
})->throws(AuthenticationException::class, 'not configured');

test('throws exception for invalid url', function () {
    $client = new KrakenClient($this->apiKey, $this->apiSecret);

    $client->optimizeUrl('not-a-valid-url');
})->throws(InvalidImageException::class, 'Invalid image URL');

test('throws exception for empty url', function () {
    $client = new KrakenClient($this->apiKey, $this->apiSecret);

    $client->optimizeUrl('');
})->throws(InvalidImageException::class, 'Invalid image URL');

test('throws exception for non-existent file', function () {
    $client = new KrakenClient($this->apiKey, $this->apiSecret);

    $client->uploadFile('/non/existent/file.jpg');
})->throws(InvalidImageException::class, 'Invalid or unreadable');

test('can create client with custom configuration', function () {
    $client = new KrakenClient(
        apiKey: 'key',
        apiSecret: 'secret',
        apiUrl: 'https://custom.api.url',
        timeout: 60,
        retryAttempts: 5
    );

    expect($client)->toBeInstanceOf(KrakenClient::class);
});
