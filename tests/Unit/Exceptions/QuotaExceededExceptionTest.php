<?php

declare(strict_types=1);

use Leviathan\Exceptions\QuotaExceededException;

test('can create quota exceeded exception', function () {
    $exception = QuotaExceededException::quotaExceeded();

    expect($exception->getMessage())->toContain('quota has been exceeded')
        ->and($exception->getCode())->toBe(429);
});

test('quota exceeded exception extends kraken exception', function () {
    $exception = QuotaExceededException::quotaExceeded();

    expect($exception)->toBeInstanceOf(\Leviathan\Exceptions\KrakenException::class);
});
