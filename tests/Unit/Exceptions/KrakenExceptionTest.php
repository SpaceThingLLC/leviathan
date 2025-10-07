<?php

declare(strict_types=1);

use Leviathan\Exceptions\KrakenException;

test('can create exception with message and code', function () {
    $exception = new KrakenException('Test error', 500);

    expect($exception->getMessage())->toBe('Test error')
        ->and($exception->getCode())->toBe(500);
});

test('can create exception with response data', function () {
    $response = ['error' => 'test', 'message' => 'Test error'];
    $exception = new KrakenException('Test error', 500, response: $response);

    expect($exception->getResponse())->toBe($response);
});

test('can create exception from API response', function () {
    $response = [
        'message' => 'API error occurred',
        'code' => 400,
        'error' => 'bad_request',
    ];

    $exception = KrakenException::fromResponse($response);

    expect($exception->getMessage())->toBe('API error occurred')
        ->and($exception->getCode())->toBe(400)
        ->and($exception->getResponse())->toBe($response);
});

test('handles missing message in API response', function () {
    $response = ['code' => 500];

    $exception = KrakenException::fromResponse($response);

    expect($exception->getMessage())->toBe('Unknown Kraken.io error')
        ->and($exception->getCode())->toBe(500);
});
