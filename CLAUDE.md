# Leviathan - Laravel Kraken.io Integration

## Project Overview

A Laravel 12 package providing a comprehensive wrapper around the Kraken.io image optimization service. This library enables seamless integration of Kraken.io's powerful image compression and manipulation capabilities into Laravel applications.

## Technical Stack

- **PHP**: 8.4+
- **Laravel**: 12.x
- **Testing**: PestPHP with 100% coverage target
- **HTTP Client**: Guzzle (Laravel HTTP facade)
- **Code Quality**: PHPStan (level 9), Laravel Pint

## Core Features

### Image Optimization
- Lossy and lossless compression
- Format conversion (WebP, AVIF, JPEG, PNG)
- Quality control and optimization profiles
- Batch processing support

### Image Manipulation
- Resizing (exact, portrait, landscape, auto, fit, crop)
- Image transformations (rotate, flip)
- Background removal
- Smart cropping

### Storage Integration
- Direct upload from URLs
- Laravel filesystem integration
- Amazon S3 support
- CloudFront integration
- Azure Blob Storage support

### Authentication
- API Key + Secret authentication
- Request signing for security
- Rate limiting awareness

## Architecture

### Package Structure
```
src/
├── LeviathanServiceProvider.php    # Service provider with config publishing
├── Facades/
│   └── Leviathan.php               # Facade for clean API access
├── Client/
│   ├── KrakenClient.php            # Core API client
│   ├── Contracts/
│   │   └── KrakenClientInterface.php
│   └── RequestBuilder.php          # Fluent request builder
├── Services/
│   ├── ImageOptimizer.php          # Image optimization service
│   ├── ImageManipulator.php        # Image manipulation service
│   └── StorageManager.php          # Storage integration
├── Exceptions/
│   ├── KrakenException.php         # Base exception
│   ├── AuthenticationException.php
│   ├── QuotaExceededException.php
│   └── InvalidImageException.php
├── DTOs/
│   ├── OptimizationOptions.php     # Optimization configuration
│   ├── ResizeOptions.php           # Resize configuration
│   └── OptimizationResult.php      # API response wrapper
└── config/
    └── leviathan.php               # Package configuration
```

### Testing Strategy

#### Unit Tests
- Individual class methods
- Request builder logic
- DTO validation
- Exception handling
- Configuration loading

#### Feature/Integration Tests
- Full API workflows with mocked responses
- Laravel service container integration
- Facade functionality
- Config publishing
- Storage integration flows

#### Test Coverage Requirements
- **Minimum**: 90% line coverage
- **Target**: 100% critical path coverage
- **PHPStan**: Level 9 strict typing
- **Architecture Tests**: PestPHP architecture testing

### Mock Strategy
- HTTP client mocking via Laravel HTTP fake
- Guzzle mock handler for unit tests
- Fixture-based response mocking
- VCR-style recording for integration tests (optional)

## Configuration

### Required Config (`config/leviathan.php`)
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
        'wait' => true, // Synchronous by default
        'lossy' => false,
        'quality' => 80,
        'format' => null, // auto-detect
    ],

    // Storage
    'storage' => [
        'disk' => env('KRAKEN_STORAGE_DISK', 's3'),
        'path' => env('KRAKEN_STORAGE_PATH', 'optimized'),
    ],
];
```

### Environment Variables
```
KRAKEN_API_KEY=your-api-key
KRAKEN_API_SECRET=your-api-secret
```

## Usage Examples

### Basic Optimization
```php
use Leviathan\Facades\Leviathan;

$result = Leviathan::optimize('https://example.com/image.jpg')
    ->lossy()
    ->quality(75)
    ->optimize();
```

### Resize & Optimize
```php
$result = Leviathan::optimize('https://example.com/image.jpg')
    ->resize(800, 600, 'auto')
    ->format('webp')
    ->optimize();
```

### Direct Upload
```php
$result = Leviathan::upload('/path/to/local/image.jpg')
    ->lossy()
    ->optimize();
```

### Laravel Storage Integration
```php
$result = Leviathan::fromDisk('public', 'images/photo.jpg')
    ->optimize()
    ->toDisk('s3', 'optimized/photo.jpg');
```

## API Endpoints

1. **POST /v1/url** ✅ - Optimize from URL (implemented)
2. **POST /v1/upload** ✅ - Direct file upload (implemented)
3. **POST /v1/userdata** ✅ - Get account status (implemented)

## Development Guidelines

### Code Standards
- PSR-12 coding standards (enforced by Pint) ✅
- Strict typing (`declare(strict_types=1)`) ✅
- PHPStan level 9 compliance ✅
- Larastan integration for Laravel-specific analysis ✅
- Descriptive method/variable names ✅
- Comprehensive PHPDoc blocks with full type annotations ✅

### Testing Requirements
- Every public method must have tests ✅
- Test both success and failure cases ✅
- Mock external HTTP calls (Guzzle MockHandler) ✅
- Test validation and edge cases ✅
- Use PestPHP's architectural testing

**Current Coverage**: 73 tests, 154 assertions, all passing

### Git Workflow
- Conventional commits
- Feature branches
- Comprehensive PR descriptions
- Automated CI/CD checks

## Dependencies

### Required
- `php: ^8.4`
- `laravel/framework: ^12.0`
- `guzzlehttp/guzzle: ^7.0`

### Development
- `pestphp/pest: ^3.0`
- `pestphp/pest-plugin-laravel: ^3.0`
- `phpstan/phpstan: ^2.0`
- `larastan/larastan: ^3.7`
- `laravel/pint: ^1.0`
- `orchestra/testbench: ^10.0`

## Implementation Phases

### Phase 1: Foundation ✅
- [x] Project structure
- [x] Service provider with type-safe config
- [x] Configuration with environment variables
- [x] Base client with authentication
- [x] Exception hierarchy (4 exception types)
- [x] Basic unit tests (73 tests passing)

### Phase 2: Core Features ✅
- [x] Image optimization API
- [x] URL-based optimization (/url endpoint)
- [x] Upload-based optimization (/upload endpoint)
- [x] Response handling & DTOs (3 DTOs)
- [x] Full test coverage (154 assertions)
- [x] PHPStan Level 9 compliance
- [x] Larastan integration

### Phase 3: Advanced Features
- [ ] Image manipulation
- [ ] Resize operations
- [ ] Format conversion
- [ ] Fluent request builder

### Phase 4: Laravel Integration
- [ ] Facade implementation
- [ ] Storage disk integration
- [ ] Queue support
- [ ] Integration tests

### Phase 5: Polish
- [ ] Comprehensive documentation
- [ ] Usage examples
- [ ] Performance optimization
- [ ] Package publishing

## Security Considerations

- Never commit API credentials
- Validate all user inputs
- Sanitize file paths
- Implement rate limiting
- Secure credential storage
- HTTPS-only API communication

## Performance Targets

- API response handling: <100ms overhead
- Memory efficient file handling
- Async queue support for batch operations
- Connection pooling for multiple requests

## Documentation Requirements

- Comprehensive README with examples
- API documentation (PHPDoc)
- Configuration guide
- Testing guide
- Contribution guidelines
- Changelog (Keep a Changelog format)
