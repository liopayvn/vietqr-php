# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-11-13

### Added
- Initial release
- Full NAPAS VietQR specification v1.5.2 compliance
- Support for all QR types:
  - QRPUSH: Payment service QR codes
  - QRCASH: ATM cash withdrawal QR codes
  - QRIBFTTA: Inter-Bank Fund Transfer to Account
  - QRIBFTTC: Inter-Bank Fund Transfer to Card
- QR code builders for all service types
- QR code parser with CRC verification
- TLV (Tag-Length-Value) encoding/decoding
- CRC16-CCITT checksum calculation and verification
- Comprehensive validation:
  - Field length constraints
  - Required field presence
  - Format validation (numeric, alphanumeric)
  - MCC code validation
  - Service code validation
- Value Objects for type safety:
  - ServiceCode
  - PointOfInitiation
- DTOs for structured data:
  - ParsedQRData
  - AdditionalDataField
- Complete test suite with 43 tests and 136 assertions
- PHPStan level 6 static analysis
- Full documentation and usage examples
