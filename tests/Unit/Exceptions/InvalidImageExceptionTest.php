<?php

declare(strict_types=1);

use Leviathan\Exceptions\InvalidImageException;

test('can create invalid url exception', function () {
    $exception = InvalidImageException::invalidUrl('not-a-url');

    expect($exception->getMessage())->toContain('Invalid image URL')
        ->and($exception->getMessage())->toContain('not-a-url')
        ->and($exception->getCode())->toBe(400);
});

test('can create invalid file exception', function () {
    $exception = InvalidImageException::invalidFile('/path/to/file.jpg');

    expect($exception->getMessage())->toContain('Invalid or unreadable')
        ->and($exception->getMessage())->toContain('/path/to/file.jpg')
        ->and($exception->getCode())->toBe(400);
});

test('can create unsupported format exception', function () {
    $exception = InvalidImageException::unsupportedFormat('bmp');

    expect($exception->getMessage())->toContain('Unsupported image format')
        ->and($exception->getMessage())->toContain('bmp')
        ->and($exception->getMessage())->toContain('jpg, png, gif, webp, avif')
        ->and($exception->getCode())->toBe(400);
});

test('invalid image exception extends kraken exception', function () {
    $exception = InvalidImageException::invalidUrl('test');

    expect($exception)->toBeInstanceOf(\Leviathan\Exceptions\KrakenException::class);
});
