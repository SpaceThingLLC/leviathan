<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Kraken.io API Credentials
    |--------------------------------------------------------------------------
    |
    | Your Kraken.io API key and secret. You can obtain these from your
    | Kraken.io account dashboard at https://kraken.io/account/api-credentials
    |
    */

    'api_key' => env('KRAKEN_API_KEY'),

    'api_secret' => env('KRAKEN_API_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | API Settings
    |--------------------------------------------------------------------------
    |
    | Configure the Kraken.io API endpoint and connection settings.
    |
    */

    'api_url' => env('KRAKEN_API_URL', 'https://api.kraken.io/v1'),

    'timeout' => env('KRAKEN_TIMEOUT', 30),

    'retry_attempts' => env('KRAKEN_RETRY_ATTEMPTS', 3),

    /*
    |--------------------------------------------------------------------------
    | Default Optimization Options
    |--------------------------------------------------------------------------
    |
    | These are the default options used for image optimization requests.
    | You can override these on a per-request basis.
    |
    */

    'defaults' => [
        // Process synchronously by default
        'wait' => true,

        // Use lossless compression by default (false)
        // Set to true for lossy compression
        'lossy' => false,

        // Quality setting for lossy compression (1-100)
        'quality' => 80,

        // Output format (null = auto-detect, or 'jpg', 'png', 'webp', 'avif')
        'format' => null,

        // Preserve metadata (EXIF, IPTC, etc.)
        'preserve_meta' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Configure Laravel filesystem integration for optimized images.
    |
    */

    'storage' => [
        // Default disk for storing optimized images
        'disk' => env('KRAKEN_STORAGE_DISK', 's3'),

        // Default path within the disk
        'path' => env('KRAKEN_STORAGE_PATH', 'optimized'),
    ],
];
