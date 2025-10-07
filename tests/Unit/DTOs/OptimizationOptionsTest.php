<?php

declare(strict_types=1);

use Leviathan\DTOs\OptimizationOptions;
use Leviathan\DTOs\ResizeOptions;

test('creates optimization options with defaults', function () {
    $options = new OptimizationOptions;

    expect($options->wait)->toBeTrue()
        ->and($options->lossy)->toBeFalse()
        ->and($options->quality)->toBeNull()
        ->and($options->format)->toBeNull()
        ->and($options->preserveMeta)->toBeFalse();
});

test('creates optimization options with custom values', function () {
    $options = new OptimizationOptions(
        wait: false,
        lossy: true,
        quality: 75,
        format: 'webp',
        preserveMeta: true
    );

    expect($options->wait)->toBeFalse()
        ->and($options->lossy)->toBeTrue()
        ->and($options->quality)->toBe(75)
        ->and($options->format)->toBe('webp')
        ->and($options->preserveMeta)->toBeTrue();
});

test('throws exception for invalid quality', function () {
    new OptimizationOptions(quality: 101);
})->throws(InvalidArgumentException::class, 'Quality must be between 1 and 100');

test('throws exception for quality below minimum', function () {
    new OptimizationOptions(quality: 0);
})->throws(InvalidArgumentException::class, 'Quality must be between 1 and 100');

test('throws exception for invalid format', function () {
    new OptimizationOptions(format: 'invalid');
})->throws(InvalidArgumentException::class, 'Invalid format');

test('converts to array correctly', function () {
    $options = new OptimizationOptions(
        wait: true,
        lossy: true,
        quality: 80,
        format: 'webp',
        preserveMeta: true
    );

    $array = $options->toArray();

    expect($array['wait'])->toBeTrue()
        ->and($array['lossy'])->toBeTrue()
        ->and($array['quality'])->toBe(80)
        ->and($array['webp'])->toBeTrue()
        ->and($array['preserve_meta'])->toBeArray();
});

test('converts to array with resize options', function () {
    $resize = new ResizeOptions(800, 600, 'auto');
    $options = new OptimizationOptions(resize: $resize);

    $array = $options->toArray();

    expect($array['resize'])->toBeArray()
        ->and($array['resize']['width'])->toBe(800)
        ->and($array['resize']['height'])->toBe(600);
});

test('converts to array with callback url', function () {
    $options = new OptimizationOptions(callbackUrl: 'https://example.com/callback');

    $array = $options->toArray();

    expect($array['callback_url'])->toBe('https://example.com/callback');
});

test('creates from array', function () {
    $options = OptimizationOptions::fromArray([
        'wait' => false,
        'lossy' => true,
        'quality' => 75,
        'format' => 'webp',
        'preserve_meta' => true,
    ]);

    expect($options->wait)->toBeFalse()
        ->and($options->lossy)->toBeTrue()
        ->and($options->quality)->toBe(75)
        ->and($options->format)->toBe('webp')
        ->and($options->preserveMeta)->toBeTrue();
});

test('creates from array with resize options', function () {
    $options = OptimizationOptions::fromArray([
        'resize' => [
            'width' => 800,
            'height' => 600,
            'strategy' => 'crop',
        ],
    ]);

    expect($options->resize)->toBeInstanceOf(ResizeOptions::class)
        ->and($options->resize->width)->toBe(800)
        ->and($options->resize->height)->toBe(600);
});

test('handles avif format correctly', function () {
    $options = new OptimizationOptions(format: 'avif');

    $array = $options->toArray();

    expect($array['avif'])->toBeTrue();
});

test('includes storage configuration', function () {
    $storage = [
        's3_store' => [
            'key' => 'xxx',
            'secret' => 'yyy',
            'bucket' => 'images',
        ],
    ];

    $options = new OptimizationOptions(storage: $storage);

    $array = $options->toArray();

    expect($array['storage'])->toBe($storage);
});
