# Leviathan

A powerful Laravel 12 package providing seamless integration with the Kraken.io image optimization service.

[![PHP Version](https://img.shields.io/badge/php-%5E8.4-blue)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/laravel-%5E12.0-red)](https://laravel.com)
[![Tests](https://img.shields.io/badge/tests-pest-green)](https://pestphp.com)

## Features

- **Image Optimization**: Lossy and lossless compression with quality control
- **Format Conversion**: WebP, AVIF, JPEG, PNG support
- **Image Manipulation**: Resize, crop, rotate, and transform images
- **Storage Integration**: Works with Laravel filesystems (S3, local, etc.)
- **Fluent API**: Clean, expressive syntax for building requests
- **Type Safety**: Full PHP 8.4 type declarations and PHPStan level 9
- **Async Support**: Queue integration for batch processing (planned)
- **Comprehensive Test Coverage**: Unit and integration tests

## Requirements

- PHP 8.4+
- Laravel 12.x
- Kraken.io API account

## Installation

> **Note**: This package is currently under active development and not yet available on Packagist.

```bash
composer require littletriumph/leviathan
```

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Leviathan\LeviathanServiceProvider"
```

Add your Kraken.io credentials to `.env`:

```env
KRAKEN_API_KEY=your-api-key
KRAKEN_API_SECRET=your-api-secret
```

## Quick Start

### Basic Optimization

```php
use Leviathan\Facades\Leviathan;

$result = Leviathan::optimize('https://example.com/image.jpg')
    ->lossy()
    ->quality(75)
    ->optimize();

echo "Original size: {$result->originalSize} bytes\n";
echo "Optimized size: {$result->krakedSize} bytes\n";
echo "Saved: {$result->savedBytes} bytes ({$result->savedPercent}%)\n";
```

### Resize & Convert Format

```php
$result = Leviathan::optimize('https://example.com/image.jpg')
    ->resize(800, 600, 'auto')
    ->format('webp')
    ->optimize();
```

### Upload Local File

```php
$result = Leviathan::upload('/path/to/local/image.jpg')
    ->lossy()
    ->quality(85)
    ->optimize();
```

### Laravel Storage Integration (Planned)

```php
// Optimize from one disk and save to another
$result = Leviathan::fromDisk('public', 'images/photo.jpg')
    ->optimize()
    ->toDisk('s3', 'optimized/photo.jpg');
```

### Batch Processing with Queues (Planned)

```php
// Dispatch to queue for async processing
Leviathan::optimize('https://example.com/large-image.jpg')
    ->lossy()
    ->queue();
```

## Configuration

The `config/leviathan.php` file contains all configuration options:

```php
return [
    // Authentication
    'api_key' => env('KRAKEN_API_KEY'),
    'api_secret' => env('KRAKEN_API_SECRET'),

    // API Settings
    'api_url' => env('KRAKEN_API_URL', 'https://api.kraken.io/v1'),
    'timeout' => env('KRAKEN_TIMEOUT', 30),
    'retry_attempts' => env('KRAKEN_RETRY_ATTEMPTS', 3),

    // Default Options
    'defaults' => [
        'wait' => true,      // Synchronous by default
        'lossy' => false,    // Lossless by default
        'quality' => 80,     // Quality setting (1-100)
    ],

    // Storage
    'storage' => [
        'disk' => env('KRAKEN_STORAGE_DISK', 's3'),
        'path' => env('KRAKEN_STORAGE_PATH', 'optimized'),
    ],
];
```

## Advanced Usage

> **Note**: Some advanced features are still in development. See [Implementation Progress](#implementation-progress) for current status.

### Optimization Options

```php
$result = Leviathan::optimize($imageUrl)
    ->lossy()              // Use lossy compression
    ->quality(80)          // Set quality (1-100)
    ->format('webp')       // Convert to WebP
    ->preserveMeta()       // Keep EXIF/metadata
    ->optimize();
```

### Resize Strategies

```php
// Auto-resize maintaining aspect ratio
Leviathan::optimize($url)->resize(800, 600, 'auto');

// Exact dimensions
Leviathan::optimize($url)->resize(800, 600, 'exact');

// Portrait orientation
Leviathan::optimize($url)->resize(800, 600, 'portrait');

// Landscape orientation
Leviathan::optimize($url)->resize(800, 600, 'landscape');

// Fit within bounds
Leviathan::optimize($url)->resize(800, 600, 'fit');

// Crop to dimensions
Leviathan::optimize($url)->resize(800, 600, 'crop');
```

### Error Handling

```php
use Leviathan\Exceptions\KrakenException;
use Leviathan\Exceptions\QuotaExceededException;

try {
    $result = Leviathan::optimize($url)->optimize();
} catch (QuotaExceededException $e) {
    // Handle quota exceeded
    Log::error('Kraken quota exceeded: ' . $e->getMessage());
} catch (KrakenException $e) {
    // Handle other Kraken errors
    Log::error('Kraken error: ' . $e->getMessage());
}
```

## Testing

Run the test suite:

```bash
composer test
```

Run with coverage:

```bash
composer test:coverage
```

Run static analysis:

```bash
composer phpstan
```

Format code:

```bash
composer format
```

## Development Status

This package is currently under active development. See [CLAUDE.md](CLAUDE.md) for detailed implementation roadmap.

### Implementation Progress

- [x] Project structure
- [x] Service provider
- [x] Configuration
- [x] Base client with authentication
- [x] Exception hierarchy
- [ ] Image optimization API
- [ ] Image manipulation features
- [ ] Laravel storage integration
- [ ] Queue support
- [ ] Comprehensive test coverage

## Security

If you discover any security-related issues, please open an issue on GitHub.

## Credits

- [littletriumph](https://github.com/littletriumph)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Links

- [Kraken.io Documentation](https://kraken.io/docs)
- [Laravel Documentation](https://laravel.com/docs)
- [Package Documentation](https://github.com/littletriumph/leviathan)