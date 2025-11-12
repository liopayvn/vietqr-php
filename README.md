# VietQR

[![Latest Version](https://img.shields.io/packagist/v/liopay/vietqr.svg)](https://packagist.org/packages/liopay/vietqr)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-blue.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/liopay/vietqr.svg?style=flat-square)](https://packagist.org/packages/liopay/vietqr)

Production-ready PHP library for building and parsing VietQR (NAPAS FastFund 24/7 IBFT) QR codes compliant with EMVCo 1.5.2 specification.

## Features

**EMVCo 1.5.2 Compliant** - Follows NAPAS QR Switching specification v1.5.2
**Build QR Codes** - Generate QR codes for all service types (PUSH/CASH/IBFT)
**Parse QR Strings** - Parse any VietQR string into structured data
**Full Validation** - Comprehensive validation per NAPAS specification
**CRC16-CCITT** - Automatic checksum calculation and verification
**Zero Dependencies** - No external runtime dependencies
**PHP 7.4+** - Strict typing throughout
**PSR-4 Autoloading** - Modern PHP package structure

## Installation

```bash
composer require liopay/vietqr
```

## Quick Start

### Build a Static Payment QR Code

```php
use Liopay\VietQR\Helper\{TLVHelper, CRCHelper};
use Liopay\VietQR\Builder\QRPushBuilder;

$tlvHelper = new TLVHelper();
$crcHelper = new CRCHelper();
$builder = new QRPushBuilder($tlvHelper, $crcHelper);

$qrString = $builder
    ->setPointOfInitiation('11') // Static QR
    ->setAcquirerBankBin('970403')
    ->setMerchantId('211299504460425')
    ->setMerchantCategoryCode('5812')
    ->setMerchantName('PHUONG CAC')
    ->setMerchantCity('HANOI')
    ->setReferenceLabel('NPS6869')
    ->build();

// Result: 00020101021138480010A00000072701300006970403011621129950446040255204581253037045802VN5910PHUONG CAC6005HANOI62110307NPS686963045802
```

### Parse a QR Code

```php
use Liopay\VietQR\Helper\{TLVHelper, CRCHelper};
use Liopay\VietQR\Parser\QRParser;

$tlvHelper = new TLVHelper();
$crcHelper = new CRCHelper();
$parser = new QRParser($tlvHelper, $crcHelper);

$qrData = $parser->parse($qrString);

echo $qrData->getMerchantName(); // "PHUONG CAC"
echo $qrData->getAmount(); // "180000"
echo $qrData->getServiceCode(); // "QRPUSH"
```

## Service Types

VietQR supports four service types:

### 1. **QRPUSH** - Payment Service
Standard merchant payment QR codes.

```php
$builder = new QRPushBuilder($tlvHelper, $crcHelper);
$qr = $builder
    ->setPointOfInitiation('12') // Dynamic
    ->setAcquirerBankBin('970403')
    ->setMerchantId('211299504460425')
    ->setMerchantCategoryCode('5812')
    ->setAmount('180000')
    ->setMerchantName('PHUONG CAC')
    ->setMerchantCity('HANOI')
    ->build();
```

### 2. **QRCASH** - ATM Cash Withdrawal
QR codes for ATM cash withdrawal. **Note**: Reference Label and Terminal Label are mandatory.

```php
$builder = new QRCashBuilder($tlvHelper, $crcHelper);
$qr = $builder
    ->setPointOfInitiation('12') // Must be dynamic
    ->setAcquirerBankBin('970403')
    ->setATMId('12345678')
    ->setCashService() // Sets service code to QRCASH
    ->setMerchantCategoryCode('6011')
    ->setMerchantName('NGUYEN HUU HUAN')
    ->setMerchantCity('HANOI')
    ->setReferenceLabel('20190109155714228384') // Required
    ->setTerminalLabel('0000111') // Required
    ->build();
```

### 3. **QRIBFTTA** - Inter-Bank Fund Transfer to Account
Peer-to-peer transfer to bank account.

```php
$builder = new QRIBFTBuilder($tlvHelper, $crcHelper);
$qr = $builder
    ->setPointOfInitiation('11') // Static
    ->setBeneficiaryBankBin('970468')
    ->setConsumerId('0011009950446')
    ->setIBFTToAccount()
    ->build();
```

### 4. **QRIBFTTC** - Inter-Bank Fund Transfer to Card
Peer-to-peer transfer to card number.

```php
$builder = new QRIBFTBuilder($tlvHelper, $crcHelper);
$qr = $builder
    ->setPointOfInitiation('12') // Dynamic
    ->setBeneficiaryBankBin('970403')
    ->setConsumerId('9704031101234567')
    ->setIBFTToCard()
    ->setAmount('180000')
    ->build();
```

## Specification Constants

### Required Values
- **AID**: `A000000727` (NAPAS Application Identifier)
- **Currency**: `704` (VND - Vietnamese Dong)
- **Country**: `VN` (Vietnam)

### Data Object IDs (Root Level)
| ID | Name | Required |
|----|------|----------|
| 00 | Payload Format Indicator | ✓ |
| 01 | Point of Initiation | Optional |
| 38 | Merchant Account Information | ✓ |
| 52 | Merchant Category Code | ✓ |
| 53 | Transaction Currency | ✓ |
| 54 | Transaction Amount | Conditional |
| 58 | Country Code | ✓ |
| 59 | Merchant Name | ✓ |
| 60 | Merchant City | ✓ |
| 62 | Additional Data Field | Optional |
| 63 | CRC Checksum | ✓ |

### Point of Initiation Values
- `11` - Static QR (reusable for multiple transactions)
- `12` - Dynamic QR (single-use transaction)

## TLV Encoding

VietQR uses TLV (Tag-Length-Value) encoding:

```
Format: ID (2 digits) + Length (2 digits) + Value (variable)
Example: "59" + "10" + "PHUONG CAC"
```

```php
$tlvHelper = new TLVHelper();

// Encode
$encoded = $tlvHelper->encode('59', 'PHUONG CAC');
// Result: "5910PHUONG CAC"

// Decode
$decoded = $tlvHelper->decode($encoded);
// Result: ['59' => 'PHUONG CAC']
```

## CRC Checksum

VietQR uses **CRC16-CCITT False**:
- Polynomial: `0x1021`
- Init Value: `0xFFFF`
- Applied to all data including `"6304"` but excluding CRC value

```php
$crcHelper = new CRCHelper();

// Calculate
$crc = $crcHelper->calculate($data);

// Verify
$isValid = $crcHelper->verify($qrString);

// Append to QR data
$completeQR = $crcHelper->append($qrData);
```

## Validation

All builders perform automatic validation:

- ✓ Field length constraints
- ✓ Required field presence
- ✓ Format validation (numeric, alphanumeric)
- ✓ MCC code validation
- ✓ Service code validation
- ✓ CRC checksum calculation
- ✓ Additional Data Field mandatory fields per QR type

## Exception Handling

```php
use Liopay\VietQR\Exception\{
    VietQRException,
    InvalidFormatException,
    InvalidCRCException,
    ValidationException,
    MissingRequiredFieldException
};

try {
    $qr = $builder->build();
} catch (MissingRequiredFieldException $e) {
    // Handle missing required field
} catch (ValidationException $e) {
    // Handle validation error
} catch (VietQRException $e) {
    // Handle general VietQR error
}
```

## Value Objects

Immutable, validated value objects for type safety:

```php
use Liopay\VietQR\ValueObject\{
    ServiceCode,
    PointOfInitiation
};

// Service Code
$serviceCode = new ServiceCode('QRPUSH');
$serviceCode->isPayment(); // true
$serviceCode->isIBFT(); // false

// Point of Initiation
$poi = PointOfInitiation::static();
$poi->isStatic(); // true
$poi->getValue(); // "11"
```

## Testing

Run the test suite:

```bash
composer test
```

Run PHPStan static analysis:

```bash
composer phpstan
```

Check code style:

```bash
composer cs-check
```

Fix code style:

```bash
composer cs-fix
```

## Requirements

- PHP >= 7.4
- ext-mbstring

## Specification Compliance

This library implements:
- **NAPAS QR Switching Technical Specifications v1.5.2**
- **EMVCo QR Code Specification for Payment Systems: Merchant-Presented Mode**

## License

MIT License. See [LICENSE](LICENSE) for details.

## Credits

Developed by [Liopay](https://liopay.vn)

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Support

For issues and questions, please use the [GitHub issue tracker](https://github.com/liopay/vietqr/issues).
