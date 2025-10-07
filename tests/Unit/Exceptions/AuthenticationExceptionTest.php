<?php

declare(strict_types=1);

use Leviathan\Exceptions\AuthenticationException;

test('can create invalid credentials exception', function () {
    $exception = AuthenticationException::invalidCredentials();

    expect($exception->getMessage())->toContain('Invalid Kraken.io API credentials')
        ->and($exception->getCode())->toBe(401);
});

test('can create missing credentials exception', function () {
    $exception = AuthenticationException::missingCredentials();

    expect($exception->getMessage())->toContain('not configured')
        ->and($exception->getMessage())->toContain('KRAKEN_API_KEY')
        ->and($exception->getCode())->toBe(401);
});

test('authentication exception extends kraken exception', function () {
    $exception = AuthenticationException::invalidCredentials();

    expect($exception)->toBeInstanceOf(\Leviathan\Exceptions\KrakenException::class);
});
