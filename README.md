# USPS PHP API

A comprehensive PHP library for integrating with the USPS Web APIs, providing easy access to shipping rates, address validation, location services, and more.

## Features

- ✅ **Address Validation** - Validate and standardize US addresses
- ✅ **Domestic Pricing** - Get shipping rates for domestic packages
- ✅ **International Pricing** - Calculate international shipping costs
- ✅ **Location Services** - Find USPS facilities and drop-off locations

## Requirements

- PHP 8.1 or higher
- USPS API credentials (Consumer Key and Consumer Secret)

## Installation

Install via Composer:

```bash
composer require colethorsen/usps-php-api
```

## Getting Started

### 1. Obtain USPS API Credentials

1. Visit the [USPS Developer Portal](https://developers.usps.com/)
2. Register for an account
3. Create a new application to get your Consumer Key and Consumer Secret
4. Note: Some APIs require additional business account setup

### 2. Basic Setup

```php
<?php
require_once 'vendor/autoload.php';

use ColeThorsen\USPS\USPS;

// Initialize the USPS client
$usps = new USPS('your-consumer-key', 'your-consumer-secret', [
    'testMode' => true, // Use false for production
]);
```

### 3. Configuration Options

```php
$usps = new USPS($consumerKey, $consumerSecret, [
    'testMode'         => true,           // Use test environment
    'timeout'          => 30,             // Request timeout in seconds
    'validateRequests' => true,           // Enable request validation
    'cache'            => $cachePool,     // PSR-6 cache instance (optional)
]);
```

## Usage Examples

### Address Validation

```php
use ColeThorsen\USPS\Enums\AddressType;

// Validate a single address
$address = [
    'streetAddress' => '1234 Main St',
    'city' => 'Los Angeles',
    'state' => 'CA',
    'ZIPCode' => '90210'
];

$result = $usps->addresses->validate($address);

// Validate multiple addresses
$addresses = [$address1, $address2, $address3];
$results = $usps->addresses->validateBatch($addresses);
```

### Domestic Pricing

```php
use ColeThorsen\USPS\Enums\MailClass;
use ColeThorsen\USPS\Enums\ProcessingCategory;
use ColeThorsen\USPS\Enums\RateIndicator;
use ColeThorsen\USPS\Enums\PriceType;

// Get base shipping rates
$params = [
    'originZIPCode'      => '90210',
    'destinationZIPCode' => '10001',
    'weight'             => 2.5,
    'length'             => 12.0,
    'width'              => 8.0,
    'height'             => 3.0,
    'mailClass'          => MailClass::PRIORITY_MAIL->value,
    'processingCategory' => ProcessingCategory::MACHINABLE->value,
    'rateIndicator'      => RateIndicator::SINGLE_PIECE->value,
    'priceType'          => PriceType::RETAIL->value,
    'mailingDate'        => '2024-01-15',
];

$rates = $usps->domesticPrices->baseRates($params);

// Get rates with extra services
use ColeThorsen\USPS\Enums\ExtraService;

$params['extraServices'] = [
    ExtraService::USPS_TRACKING_ELECTRONIC->value,
    ExtraService::SIGNATURE_CONFIRMATION->value,
];

$ratesWithServices = $usps->domesticPrices->extraServiceRates($params);
```

### International Pricing

```php
use ColeThorsen\USPS\Enums\DestinationEntryFacilityType;

$params = [
    'originZIPCode'                => '90210',
    'destinationCountryCode'       => 'GB',
    'foreignPostalCode'            => 'SW1A 1AA',
    'destinationEntryFacilityType' => DestinationEntryFacilityType::INTERNATIONAL_SERVICE_CENTER->value,
    'weight'                       => 2.0,
    'length'                       => 12.0,
    'width'                        => 8.0,
    'height'                       => 3.0,
    'mailClass'                    => MailClass::PRIORITY_MAIL_INTERNATIONAL->value,
    'processingCategory'           => ProcessingCategory::MACHINABLE->value,
    'rateIndicator'                => RateIndicator::SINGLE_PIECE->value,
    'priceType'                    => PriceType::RETAIL->value,
    'mailingDate'                  => '2024-01-15',
];

$internationalRates = $usps->internationalPrices->baseRates($params);
```

### Location Services

```php
// Find USPS locations
$params = [
    'ZIPCode' => '90210',
    'radius'  => 10,
];

$locations = $usps->locations->search($params);

// Get dropoff locations
$dropoffParams = [
    'ZIPCode' => '90210',
    'radius'  => 5,
];

$dropoffLocations = $usps->locations->dropoffLocations($dropoffParams);
```

## Available Enums

The library provides comprehensive enums for type safety:

- `MailClass` - Available mail classes (Priority Mail, Ground Advantage, etc.)
- `ProcessingCategory` - Package processing categories (Machinable, Letters, etc.)
- `RateIndicator` - Rate indicators (Single Piece, Bulk, etc.)
- `PriceType` - Pricing types (Retail, Commercial, etc.)
- `ExtraService` - Additional services (Tracking, Insurance, Signature, etc.)
- `UnitOfMeasurement` - Weight and dimension units
- `FacilityType` - USPS facility types
- `AddressType` - Address classification types

## Testing

The library includes comprehensive tests. To run them:

### 1. Set Up Test Environment

Create a `.env` file in the project root:

```env
USPS_CONSUMER_KEY=your-test-consumer-key
USPS_CONSUMER_SECRET=your-test-consumer-secret
```

### 2. Run Tests

```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test suite
./vendor/bin/phpunit tests/Services/AddressesTest.php

# Run with coverage (requires Xdebug)
./vendor/bin/phpunit --coverage-html coverage-report
```

### 3. Test Configuration

The test suite includes:
- Unit tests for all services
- Integration tests with USPS APIs
- Validation tests for request/response schemas
- Error handling tests

## Error Handling

The library provides specific exception types:

```php
use ColeThorsen\USPS\Exceptions\ValidationException;
use ColeThorsen\USPS\Exceptions\TechnicalException;
use ColeThorsen\USPS\Exceptions\USPSException;

try {
    $result = $usps->addresses->validate($address);
} catch (ValidationException $e) {
    // Request validation failed
    echo "Validation Error: " . $e->getMessage();
} catch (TechnicalException $e) {
    // API or technical error
    echo "Technical Error: " . $e->getMessage();
} catch (USPSException $e) {
    // General USPS API error
    echo "USPS Error: " . $e->getMessage();
}
```

## Advanced Configuration

### Custom Cache Implementation

Implement PSR-6 caching for improved performance:

```php
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

$cache = new FilesystemAdapter();
$usps = new USPS($key, $secret, ['cache' => $cache]);
```

### Request Validation

Control request validation behavior:

```php
// Disable validation for faster requests (not recommended for production)
$usps = new USPS($key, $secret, ['validateRequests' => false]);
```

### Custom Timeout

Adjust request timeouts:

```php
$usps = new USPS($key, $secret, ['timeout' => 60]);
```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/feature`)
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass (`./vendor/bin/phpunit`)
6. Commit your changes (`git commit -m 'Add feature'`)
7. Push to the branch (`git push origin feature/feature`)
8. Open a Pull Request

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Support

- Issues: [GitHub Issues](https://github.com/colethorsen/usps-php-api/issues)
- Documentation: [API Documentation](https://developers.usps.com/)

## Changelog

### v0.1.0 (Current)
- Initial release
- Address validation support
- Domestic and international pricing
- Location services
- Comprehensive test suite
- Full enum support for type safety

---

**Note**: This library is not officially affiliated with the United States Postal Service. USPS is a trademark of the United States Postal Service.