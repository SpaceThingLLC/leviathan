<?php

declare(strict_types=1);

use Leviathan\DTOs\ResizeOptions;

test('creates resize options with required values', function () {
    $options = new ResizeOptions(800, 600);

    expect($options->width)->toBe(800)
        ->and($options->height)->toBe(600)
        ->and($options->strategy)->toBe('auto')
        ->and($options->background)->toBeNull()
        ->and($options->enhance)->toBeFalse();
});

test('creates resize options with custom strategy', function () {
    $options = new ResizeOptions(800, 600, 'crop');

    expect($options->strategy)->toBe('crop');
});

test('creates resize options with background color', function () {
    $options = new ResizeOptions(800, 600, 'fill', '#FF0000');

    expect($options->background)->toBe('#FF0000');
});

test('accepts background color without hash', function () {
    $options = new ResizeOptions(800, 600, 'fill', 'FF0000');

    expect($options->background)->toBe('FF0000');
});

test('creates resize options with enhance enabled', function () {
    $options = new ResizeOptions(800, 600, enhance: true);

    expect($options->enhance)->toBeTrue();
});

test('throws exception for invalid width', function () {
    new ResizeOptions(0, 600);
})->throws(InvalidArgumentException::class, 'Width and height must be positive');

test('throws exception for invalid height', function () {
    new ResizeOptions(800, -1);
})->throws(InvalidArgumentException::class, 'Width and height must be positive');

test('throws exception for invalid strategy', function () {
    new ResizeOptions(800, 600, 'invalid');
})->throws(InvalidArgumentException::class, 'Invalid resize strategy');

test('throws exception for invalid background color', function () {
    new ResizeOptions(800, 600, 'fill', 'invalid');
})->throws(InvalidArgumentException::class, 'Background must be a valid hex color');

test('accepts all valid strategies', function ($strategy) {
    $options = new ResizeOptions(800, 600, $strategy);

    expect($options->strategy)->toBe($strategy);
})->with(['exact', 'portrait', 'landscape', 'auto', 'fit', 'crop', 'square', 'fill']);

test('converts to array correctly', function () {
    $options = new ResizeOptions(800, 600, 'crop');

    $array = $options->toArray();

    expect($array['width'])->toBe(800)
        ->and($array['height'])->toBe(600)
        ->and($array['strategy'])->toBe('crop');
});

test('converts to array with background color without hash', function () {
    $options = new ResizeOptions(800, 600, 'fill', '#FF0000');

    $array = $options->toArray();

    expect($array['background'])->toBe('FF0000');
});

test('converts to array with enhance', function () {
    $options = new ResizeOptions(800, 600, enhance: true);

    $array = $options->toArray();

    expect($array['enhance'])->toBeTrue();
});

test('creates from array', function () {
    $options = ResizeOptions::fromArray([
        'width' => 800,
        'height' => 600,
        'strategy' => 'crop',
        'background' => '#FF0000',
        'enhance' => true,
    ]);

    expect($options->width)->toBe(800)
        ->and($options->height)->toBe(600)
        ->and($options->strategy)->toBe('crop')
        ->and($options->background)->toBe('#FF0000')
        ->and($options->enhance)->toBeTrue();
});

test('throws exception when creating from array without width', function () {
    ResizeOptions::fromArray(['height' => 600]);
})->throws(InvalidArgumentException::class, 'Width is required');

test('throws exception when creating from array without height', function () {
    ResizeOptions::fromArray(['width' => 800]);
})->throws(InvalidArgumentException::class, 'Height is required');

test('uses default strategy when not provided in array', function () {
    $options = ResizeOptions::fromArray(['width' => 800, 'height' => 600]);

    expect($options->strategy)->toBe('auto');
});
