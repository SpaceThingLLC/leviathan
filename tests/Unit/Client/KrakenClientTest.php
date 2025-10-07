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
})->throws(AuthenticationException::class, AuthenticationException::missingCredentials()->getMessage());

test('throws exception when api secret is missing', function () {
    new KrakenClient('key', '');
})->throws(AuthenticationException::class, AuthenticationException::missingCredentials()->getMessage());

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

test('successfully optimizes image from url', function () {
    $mockResponse = [
        'success' => true,
        'file_name' => 'test.jpg',
        'original_size' => 1000000,
        'kraked_size' => 500000,
        'saved_bytes' => 500000,
        'kraked_url' => 'https://kraken.io/optimized/test.jpg',
    ];

    $mock = new MockHandler([
        new Response(200, [], json_encode($mockResponse)),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $httpClient = new Client(['handler' => $handlerStack]);

    $client = new KrakenClient($this->apiKey, $this->apiSecret);

    // Use reflection to inject mock client
    $reflection = new \ReflectionClass($client);
    $property = $reflection->getProperty('httpClient');
    $property->setAccessible(true);
    $property->setValue($client, $httpClient);

    $result = $client->optimizeUrl('https://example.com/test.jpg');

    expect($result)->toBe($mockResponse);
});

test('successfully optimizes image with options', function () {
    $mockResponse = [
        'success' => true,
        'file_name' => 'test.jpg',
        'original_size' => 1000000,
        'kraked_size' => 400000,
        'saved_bytes' => 600000,
        'kraked_url' => 'https://kraken.io/optimized/test.jpg',
    ];

    $mock = new MockHandler([
        new Response(200, [], json_encode($mockResponse)),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $httpClient = new Client(['handler' => $handlerStack]);

    $client = new KrakenClient($this->apiKey, $this->apiSecret);

    $reflection = new \ReflectionClass($client);
    $property = $reflection->getProperty('httpClient');
    $property->setAccessible(true);
    $property->setValue($client, $httpClient);

    $result = $client->optimizeUrl('https://example.com/test.jpg', [
        'lossy' => true,
        'quality' => 75,
        'wait' => true,
    ]);

    expect($result)->toBe($mockResponse);
});

test('handles api authentication error', function () {
    $mockResponse = [
        'success' => false,
        'error' => 'Authentication failed',
        'message' => 'Unauthorized - Invalid API credentials',
    ];

    $mock = new MockHandler([
        new Response(200, [], json_encode($mockResponse)),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $httpClient = new Client(['handler' => $handlerStack]);

    $client = new KrakenClient($this->apiKey, $this->apiSecret);

    $reflection = new \ReflectionClass($client);
    $property = $reflection->getProperty('httpClient');
    $property->setAccessible(true);
    $property->setValue($client, $httpClient);

    $client->optimizeUrl('https://example.com/test.jpg');
})->throws(AuthenticationException::class);

test('handles api quota exceeded error', function () {
    $mockResponse = [
        'success' => false,
        'error' => 'Quota exceeded',
        'message' => 'Your quota has been exceeded',
    ];

    $mock = new MockHandler([
        new Response(200, [], json_encode($mockResponse)),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $httpClient = new Client(['handler' => $handlerStack]);

    $client = new KrakenClient($this->apiKey, $this->apiSecret);

    $reflection = new \ReflectionClass($client);
    $property = $reflection->getProperty('httpClient');
    $property->setAccessible(true);
    $property->setValue($client, $httpClient);

    $client->optimizeUrl('https://example.com/test.jpg');
})->throws(\Leviathan\Exceptions\QuotaExceededException::class);

test('retries on network failure', function () {
    $mockResponse = [
        'success' => true,
        'file_name' => 'test.jpg',
        'original_size' => 1000000,
        'kraked_size' => 500000,
        'saved_bytes' => 500000,
        'kraked_url' => 'https://kraken.io/optimized/test.jpg',
    ];

    // First two requests fail, third succeeds
    $mock = new MockHandler([
        new \GuzzleHttp\Exception\ConnectException('Connection failed', new \GuzzleHttp\Psr7\Request('POST', '/url')),
        new \GuzzleHttp\Exception\ConnectException('Connection failed', new \GuzzleHttp\Psr7\Request('POST', '/url')),
        new Response(200, [], json_encode($mockResponse)),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $httpClient = new Client(['handler' => $handlerStack]);

    $client = new KrakenClient($this->apiKey, $this->apiSecret);

    $reflection = new \ReflectionClass($client);
    $property = $reflection->getProperty('httpClient');
    $property->setAccessible(true);
    $property->setValue($client, $httpClient);

    $result = $client->optimizeUrl('https://example.com/test.jpg');

    expect($result)->toBe($mockResponse);
});

test('throws exception after max retries', function () {
    $mock = new MockHandler([
        new \GuzzleHttp\Exception\ConnectException('Connection failed', new \GuzzleHttp\Psr7\Request('POST', '/url')),
        new \GuzzleHttp\Exception\ConnectException('Connection failed', new \GuzzleHttp\Psr7\Request('POST', '/url')),
        new \GuzzleHttp\Exception\ConnectException('Connection failed', new \GuzzleHttp\Psr7\Request('POST', '/url')),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $httpClient = new Client(['handler' => $handlerStack]);

    $client = new KrakenClient($this->apiKey, $this->apiSecret);

    $reflection = new \ReflectionClass($client);
    $property = $reflection->getProperty('httpClient');
    $property->setAccessible(true);
    $property->setValue($client, $httpClient);

    $client->optimizeUrl('https://example.com/test.jpg');
})->throws(KrakenException::class, 'Failed to connect');

test('handles invalid json response', function () {
    $mock = new MockHandler([
        new Response(200, [], 'invalid json'),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $httpClient = new Client(['handler' => $handlerStack]);

    $client = new KrakenClient($this->apiKey, $this->apiSecret);

    $reflection = new \ReflectionClass($client);
    $property = $reflection->getProperty('httpClient');
    $property->setAccessible(true);
    $property->setValue($client, $httpClient);

    $client->optimizeUrl('https://example.com/test.jpg');
})->throws(KrakenException::class, 'Invalid JSON response');

test('getUserData returns account information', function () {
    $mockResponse = [
        'success' => true,
        'plan_name' => 'Enterprise',
        'quota_used' => 1000000,
        'quota_total' => 10000000,
        'quota_remaining' => 9000000,
    ];

    $mock = new MockHandler([
        new Response(200, [], json_encode($mockResponse)),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $httpClient = new Client(['handler' => $handlerStack]);

    $client = new KrakenClient($this->apiKey, $this->apiSecret);

    $reflection = new \ReflectionClass($client);
    $property = $reflection->getProperty('httpClient');
    $property->setAccessible(true);
    $property->setValue($client, $httpClient);

    $result = $client->getUserData();

    expect($result)->toBe($mockResponse);
});
