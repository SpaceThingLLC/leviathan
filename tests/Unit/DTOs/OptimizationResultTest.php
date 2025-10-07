<?php

declare(strict_types=1);

use Leviathan\DTOs\OptimizationResult;

test('creates optimization result with all fields', function () {
    $result = new OptimizationResult(
        success: true,
        fileName: 'test.jpg',
        originalSize: 1000000,
        krakedSize: 500000,
        savedBytes: 500000,
        krakedUrl: 'https://kraken.io/optimized/test.jpg',
        originalWidth: 1920,
        originalHeight: 1080,
        krakedWidth: 1920,
        krakedHeight: 1080,
        originalType: 'image/jpeg',
        krakedType: 'image/jpeg'
    );

    expect($result->success)->toBeTrue()
        ->and($result->fileName)->toBe('test.jpg')
        ->and($result->originalSize)->toBe(1000000)
        ->and($result->krakedSize)->toBe(500000)
        ->and($result->savedBytes)->toBe(500000)
        ->and($result->krakedUrl)->toBe('https://kraken.io/optimized/test.jpg');
});

test('calculates saved percentage correctly', function () {
    $result = new OptimizationResult(
        success: true,
        fileName: 'test.jpg',
        originalSize: 1000000,
        krakedSize: 500000,
        savedBytes: 500000,
        krakedUrl: 'https://kraken.io/optimized/test.jpg'
    );

    expect($result->getSavedPercentage())->toBe(50.0);
});

test('handles zero original size for percentage', function () {
    $result = new OptimizationResult(
        success: true,
        fileName: 'test.jpg',
        originalSize: 0,
        krakedSize: 0,
        savedBytes: 0,
        krakedUrl: 'https://kraken.io/optimized/test.jpg'
    );

    expect($result->getSavedPercentage())->toBe(0.0);
});

test('detects when image was resized', function () {
    $result = new OptimizationResult(
        success: true,
        fileName: 'test.jpg',
        originalSize: 1000000,
        krakedSize: 500000,
        savedBytes: 500000,
        krakedUrl: 'https://kraken.io/optimized/test.jpg',
        originalWidth: 1920,
        originalHeight: 1080,
        krakedWidth: 800,
        krakedHeight: 600
    );

    expect($result->wasResized())->toBeTrue();
});

test('detects when image was not resized', function () {
    $result = new OptimizationResult(
        success: true,
        fileName: 'test.jpg',
        originalSize: 1000000,
        krakedSize: 500000,
        savedBytes: 500000,
        krakedUrl: 'https://kraken.io/optimized/test.jpg',
        originalWidth: 1920,
        originalHeight: 1080,
        krakedWidth: 1920,
        krakedHeight: 1080
    );

    expect($result->wasResized())->toBeFalse();
});

test('detects when format was converted', function () {
    $result = new OptimizationResult(
        success: true,
        fileName: 'test.jpg',
        originalSize: 1000000,
        krakedSize: 500000,
        savedBytes: 500000,
        krakedUrl: 'https://kraken.io/optimized/test.webp',
        originalType: 'image/jpeg',
        krakedType: 'image/webp'
    );

    expect($result->wasConverted())->toBeTrue();
});

test('detects when format was not converted', function () {
    $result = new OptimizationResult(
        success: true,
        fileName: 'test.jpg',
        originalSize: 1000000,
        krakedSize: 500000,
        savedBytes: 500000,
        krakedUrl: 'https://kraken.io/optimized/test.jpg',
        originalType: 'image/jpeg',
        krakedType: 'image/jpeg'
    );

    expect($result->wasConverted())->toBeFalse();
});

test('creates from api response', function () {
    $response = [
        'success' => true,
        'file_name' => 'test.jpg',
        'original_size' => 1000000,
        'kraked_size' => 500000,
        'saved_bytes' => 500000,
        'kraked_url' => 'https://kraken.io/optimized/test.jpg',
        'original_width' => 1920,
        'original_height' => 1080,
        'kraked_width' => 800,
        'kraked_height' => 600,
        'original_type' => 'image/jpeg',
        'kraked_type' => 'image/webp',
    ];

    $result = OptimizationResult::fromResponse($response);

    expect($result->success)->toBeTrue()
        ->and($result->fileName)->toBe('test.jpg')
        ->and($result->originalSize)->toBe(1000000)
        ->and($result->krakedSize)->toBe(500000)
        ->and($result->originalWidth)->toBe(1920)
        ->and($result->krakedWidth)->toBe(800)
        ->and($result->originalType)->toBe('image/jpeg')
        ->and($result->krakedType)->toBe('image/webp');
});

test('creates from incomplete api response', function () {
    $response = [
        'success' => true,
        'file_name' => 'test.jpg',
        'kraked_url' => 'https://kraken.io/optimized/test.jpg',
    ];

    $result = OptimizationResult::fromResponse($response);

    expect($result->success)->toBeTrue()
        ->and($result->fileName)->toBe('test.jpg')
        ->and($result->originalSize)->toBe(0)
        ->and($result->krakedSize)->toBe(0)
        ->and($result->originalWidth)->toBeNull();
});

test('converts to array with all data', function () {
    $result = new OptimizationResult(
        success: true,
        fileName: 'test.jpg',
        originalSize: 1000000,
        krakedSize: 500000,
        savedBytes: 500000,
        krakedUrl: 'https://kraken.io/optimized/test.jpg',
        originalWidth: 1920,
        originalHeight: 1080,
        krakedWidth: 800,
        krakedHeight: 600,
        originalType: 'image/jpeg',
        krakedType: 'image/webp'
    );

    $array = $result->toArray();

    expect($array['success'])->toBeTrue()
        ->and($array['file_name'])->toBe('test.jpg')
        ->and($array['saved_percentage'])->toBe(50.0)
        ->and($array['was_resized'])->toBeTrue()
        ->and($array['was_converted'])->toBeTrue();
});
