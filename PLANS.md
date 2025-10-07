# Implementation Plans

## Phase 1: Foundation ✅ COMPLETED

**Branch**: `phase-1-foundation`
**Status**: Merged to main

### Completed Items
- [x] Project structure and directories
- [x] composer.json with PHP 8.4, Laravel 12, PestPHP dependencies
- [x] Service provider with auto-discovery and config publishing
- [x] Configuration file with API credentials and defaults
- [x] Exception hierarchy (KrakenException, AuthenticationException, QuotaExceededException, InvalidImageException)
- [x] Base KrakenClient with authentication
- [x] PestPHP testing infrastructure (Pest.php, TestCase, phpunit.xml)
- [x] Code quality tools (PHPStan level 9, Pint configuration)
- [x] Comprehensive unit tests for exceptions and client validation

### Key Files Created
- `src/LeviathanServiceProvider.php` - Service provider with singleton binding
- `src/Client/KrakenClient.php` - Core API client with retry logic
- `src/Client/Contracts/KrakenClientInterface.php` - Client interface
- `src/Exceptions/` - Complete exception hierarchy
- `config/leviathan.php` - Package configuration
- `tests/Unit/` - Exception and client validation tests

---

## Phase 2: Core Features 🚧 IN PROGRESS

**Branch**: `phase-2-core-features`
**Objective**: Implement core image optimization features with full API integration

### Tasks

#### 2.1 DTOs (Data Transfer Objects)
- [ ] Create `OptimizationOptions` DTO
  - Properties: lossy, quality, wait, format, preserve_meta
  - Validation methods
  - Array conversion methods
- [ ] Create `ResizeOptions` DTO
  - Properties: width, height, strategy (auto/exact/portrait/landscape/fit/crop)
  - Validation for dimensions and strategy
- [ ] Create `OptimizationResult` DTO
  - Properties: success, file_name, original_size, kraked_size, saved_bytes, kraked_url
  - Calculated property: saved_percent
  - Factory method from API response

#### 2.2 Enhanced KrakenClient
- [ ] Refactor `optimizeUrl()` to use DTOs
  - Accept OptimizationOptions and ResizeOptions
  - Build proper request payload
  - Return OptimizationResult DTO
- [ ] Refactor `uploadFile()` to use DTOs
  - Accept OptimizationOptions and ResizeOptions
  - Handle multipart form data with DTOs
  - Return OptimizationResult DTO
- [ ] Improve error handling
  - Parse all Kraken API error codes
  - Map to appropriate exception types
  - Include detailed error context

#### 2.3 Request Builder (Fluent Interface)
- [ ] Create `RequestBuilder` class
  - Fluent methods: `lossy()`, `quality()`, `format()`, `preserveMeta()`
  - Resize methods: `resize()`, `exact()`, `auto()`, `portrait()`, `landscape()`, `fit()`, `crop()`
  - Build methods: `buildOptimizationOptions()`, `buildResizeOptions()`
- [ ] Integrate RequestBuilder with KrakenClient
  - Factory method on client: `client->optimize($url)` returns builder
  - Terminal method: `builder->execute()` calls client

#### 2.4 Service Layer
- [ ] Create `ImageOptimizer` service
  - High-level optimization methods
  - Integration with KrakenClient
  - Default options from config
- [ ] Create `ImageManipulator` service (for Phase 3 resize features)
  - Resize strategy implementations
  - Validation for dimensions

#### 2.5 HTTP Client Integration Tests
- [ ] Create mock responses for Kraken API
  - Success responses (URL optimization, file upload)
  - Error responses (authentication, quota, invalid image)
  - Edge cases (network errors, timeouts, retries)
- [ ] Test `optimizeUrl()` integration
  - Successful optimization
  - Error handling
  - Retry logic
- [ ] Test `uploadFile()` integration
  - Successful upload and optimization
  - Invalid file handling
  - Multipart form data validation
- [ ] Test `getUserData()` integration
  - Successful user data retrieval
  - Authentication errors

#### 2.6 Unit Tests for New Components
- [ ] DTOs unit tests
  - OptimizationOptions validation
  - ResizeOptions validation
  - OptimizationResult factory and calculations
- [ ] RequestBuilder unit tests
  - Fluent interface behavior
  - Option building and validation
  - Integration with client
- [ ] Service layer unit tests
  - ImageOptimizer behavior
  - Default options handling

### Success Criteria
- ✅ All DTOs properly validate input
- ✅ KrakenClient returns typed DTOs instead of raw arrays
- ✅ Request builder provides clean, fluent API
- ✅ 90%+ test coverage maintained
- ✅ PHPStan level 9 passes
- ✅ Integration tests with mocked HTTP responses
- ✅ All error scenarios properly handled

### Files to Create
```
src/
├── DTOs/
│   ├── OptimizationOptions.php
│   ├── ResizeOptions.php
│   └── OptimizationResult.php
├── Services/
│   ├── ImageOptimizer.php
│   └── ImageManipulator.php
└── Client/
    └── RequestBuilder.php

tests/
├── Unit/
│   ├── DTOs/
│   │   ├── OptimizationOptionsTest.php
│   │   ├── ResizeOptionsTest.php
│   │   └── OptimizationResultTest.php
│   ├── Client/
│   │   └── RequestBuilderTest.php
│   └── Services/
│       ├── ImageOptimizerTest.php
│       └── ImageManipulatorTest.php
└── Feature/
    └── KrakenClientIntegrationTest.php
```

---

## Phase 3: Advanced Features (Planned)

**Objective**: Add advanced image manipulation and format conversion

### Planned Tasks
- [ ] Image manipulation methods (rotate, flip, etc.)
- [ ] Advanced resize strategies with smart cropping
- [ ] Format conversion (WebP, AVIF optimization)
- [ ] Metadata preservation options
- [ ] Callback URL support for async operations

---

## Phase 4: Laravel Integration (Planned)

**Objective**: Deep integration with Laravel ecosystem

### Planned Tasks
- [ ] Facade implementation
- [ ] Laravel Storage disk integration
- [ ] Queue support for async processing
- [ ] Event dispatching (OptimizationStarted, OptimizationCompleted)
- [ ] Artisan commands (kraken:optimize, kraken:quota)
- [ ] Middleware for automatic image optimization

---

## Phase 5: Polish & Release (Planned)

**Objective**: Production-ready package with comprehensive documentation

### Planned Tasks
- [ ] Complete API documentation
- [ ] Usage examples and cookbook
- [ ] Performance optimization
- [ ] CI/CD pipeline (GitHub Actions)
- [ ] Package publishing to Packagist
- [ ] Changelog and versioning
- [ ] Contributing guidelines

---

## Notes

### API Endpoints Reference
1. **POST /v1/url** - Optimize image from URL
2. **POST /v1/upload** - Upload and optimize image file
3. **POST /v1/userdata** - Get account information and quota

### Testing Strategy
- **Unit Tests**: Individual class methods, validation, DTOs
- **Feature/Integration Tests**: Full workflows with mocked HTTP responses
- **Coverage Target**: 90%+ minimum
- **Static Analysis**: PHPStan level 9 strict mode

### Code Quality Standards
- PSR-12 coding standards (enforced by Pint)
- Strict typing (`declare(strict_types=1)`)
- Comprehensive PHPDoc blocks
- Descriptive method and variable names
