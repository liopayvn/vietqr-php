# VietQR

[![Latest Version](https://img.shields.io/packagist/v/liopay/vietqr.svg)](https://packagist.org/packages/liopay/vietqr)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-blue.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/liopay/vietqr.svg?style=flat-square)](https://packagist.org/packages/liopay/vietqr)

**[English](README.md)** | **Tiếng Việt**

Thư viện PHP sẵn sàng cho môi trường production để tạo và phân tích mã QR VietQR (NAPAS FastFund 24/7 IBFT) tuân thủ đặc tả EMVCo 1.5.2.

## Tính năng

- **Tuân thủ EMVCo 1.5.2** - Theo đặc tả NAPAS QR Switching v1.5.2
- **Tạo mã QR** - Tạo mã QR cho tất cả các loại dịch vụ (PUSH/CASH/IBFT)
- **Phân tích chuỗi QR** - Phân tích bất kỳ chuỗi VietQR nào thành dữ liệu có cấu trúc
- **Xác thực đầy đủ** - Xác thực toàn diện theo đặc tả NAPAS
- **CRC16-CCITT** - Tự động tính toán và xác minh checksum
- **Không phụ thuộc bên ngoài** - Không có phụ thuộc runtime bên ngoài
- **PHP 7.4+** - Strict typing trong toàn bộ code
- **PSR-4 Autoloading** - Cấu trúc package PHP hiện đại

## Cài đặt

```bash
composer require liopay/vietqr
```

## Bắt đầu nhanh

### Tạo mã QR thanh toán tĩnh

```php
use Liopay\VietQR\Builder\QRPushBuilder;

$builder = new QRPushBuilder();

$qrString = $builder
    ->setPointOfInitiation('11') // QR tĩnh
    ->setAcquirerBankBin('970436')
    ->setMerchantId('1017595600')
    ->setMerchantCategoryCode('5812')
    ->setMerchantName('NGO QUOC DAT')
    ->setMerchantCity('HANOI')
    ->setReferenceLabel('NPS6869')
    ->build();

// Kết quả: 00020101021138480010A000000727013000069704360114101759560025520458125303704580 2VN5912NGO QUOC DAT6005HANOI62110307NPS686963045802
```

### Phân tích mã QR

```php
use Liopay\VietQR\Parser\QRParser;

$parser = new QRParser();

$qrData = $parser->parse($qrString);

echo $qrData->getMerchantName(); // "NGO QUOC DAT"
echo $qrData->getAmount(); // "180000"
echo $qrData->getServiceCode(); // "QRPUSH"
```

## Các loại dịch vụ

VietQR hỗ trợ bốn loại dịch vụ:

### 1. **QRPUSH** - Dịch vụ thanh toán
Mã QR thanh toán merchant tiêu chuẩn.

```php
$builder = new QRPushBuilder();
$qr = $builder
    ->setPointOfInitiation('12') // QR động
    ->setAcquirerBankBin('970436')
    ->setMerchantId('1017595600')
    ->setMerchantCategoryCode('5812')
    ->setAmount('180000')
    ->setMerchantName('NGO QUOC DAT')
    ->setMerchantCity('HANOI')
    ->build();
```

### 2. **QRCASH** - Rút tiền mặt ATM
Mã QR để rút tiền mặt tại ATM. **Lưu ý**: Reference Label và Terminal Label là bắt buộc.

```php
$builder = new QRCashBuilder();
$qr = $builder
    ->setPointOfInitiation('12') // Phải là QR động
    ->setAcquirerBankBin('970436')
    ->setATMId('12345678')
    ->setCashService() // Đặt mã dịch vụ thành QRCASH
    ->setMerchantCategoryCode('6011')
    ->setMerchantName('NGO QUOC DAT')
    ->setMerchantCity('HANOI')
    ->setReferenceLabel('20190109155714228384') // Bắt buộc
    ->setTerminalLabel('0000111') // Bắt buộc
    ->build();
```

### 3. **QRIBFTTA** - Chuyển khoản liên ngân hàng đến tài khoản
Chuyển khoản ngang hàng đến tài khoản ngân hàng.

```php
$builder = new QRIBFTBuilder();
$qr = $builder
    ->setPointOfInitiation('11') // QR tĩnh
    ->setBeneficiaryBankBin('970436')
    ->setConsumerId('1017595600')
    ->setIBFTToAccount()
    ->build();
```

### 4. **QRIBFTTC** - Chuyển khoản liên ngân hàng đến thẻ
Chuyển khoản ngang hàng đến số thẻ.

```php
$builder = new QRIBFTBuilder();
$qr = $builder
    ->setPointOfInitiation('12') // QR động
    ->setBeneficiaryBankBin('970436')
    ->setConsumerId('9704361017595600')
    ->setIBFTToCard()
    ->setAmount('180000')
    ->build();
```

## Các hằng số theo đặc tả

### Giá trị bắt buộc
- **AID**: `A000000727` (NAPAS Application Identifier)
- **Tiền tệ**: `704` (VND - Đồng Việt Nam)
- **Quốc gia**: `VN` (Việt Nam)

### ID đối tượng dữ liệu (Root Level)
| ID | Tên | Bắt buộc |
|----|-----|----------|
| 00 | Payload Format Indicator | ✓ |
| 01 | Point of Initiation | Tùy chọn |
| 38 | Merchant Account Information | ✓ |
| 52 | Merchant Category Code | ✓ |
| 53 | Transaction Currency | ✓ |
| 54 | Transaction Amount | Có điều kiện |
| 58 | Country Code | ✓ |
| 59 | Merchant Name | ✓ |
| 60 | Merchant City | ✓ |
| 62 | Additional Data Field | Tùy chọn |
| 63 | CRC Checksum | ✓ |

### Giá trị Point of Initiation
- `11` - QR tĩnh (có thể tái sử dụng cho nhiều giao dịch)
- `12` - QR động (giao dịch một lần)

## Mã hóa TLV

VietQR sử dụng mã hóa TLV (Tag-Length-Value):

```
Định dạng: ID (2 chữ số) + Độ dài (2 chữ số) + Giá trị (biến đổi)
Ví dụ: "59" + "12" + "NGO QUOC DAT"
```

```php
$tlvHelper = new TLVHelper();

// Mã hóa
$encoded = $tlvHelper->encode('59', 'NGO QUOC DAT');
// Kết quả: "5912NGO QUOC DAT"

// Giải mã
$decoded = $tlvHelper->decode($encoded);
// Kết quả: ['59' => 'NGO QUOC DAT']
```

## CRC Checksum

VietQR sử dụng **CRC16-CCITT False**:
- Polynomial: `0x1021`
- Init Value: `0xFFFF`
- Áp dụng cho tất cả dữ liệu bao gồm `"6304"` nhưng loại trừ giá trị CRC

```php
$crcHelper = new CRCHelper();

// Tính toán
$crc = $crcHelper->calculate($data);

// Xác minh
$isValid = $crcHelper->verify($qrString);

// Thêm vào dữ liệu QR
$completeQR = $crcHelper->append($qrData);
```

## Xác thực

Tất cả các builder thực hiện xác thực tự động:

- ✓ Ràng buộc độ dài trường
- ✓ Sự hiện diện của trường bắt buộc
- ✓ Xác thực định dạng (số, chữ và số)
- ✓ Xác thực mã MCC
- ✓ Xác thực mã dịch vụ
- ✓ Tính toán CRC checksum
- ✓ Các trường bắt buộc trong Additional Data Field theo từng loại QR

## Xử lý ngoại lệ

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
    // Xử lý trường bắt buộc bị thiếu
} catch (ValidationException $e) {
    // Xử lý lỗi xác thực
} catch (VietQRException $e) {
    // Xử lý lỗi VietQR chung
}
```

## Value Objects

Các value object bất biến, đã được xác thực để đảm bảo type safety:

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

Chạy test suite:

```bash
composer test
```

Chạy phân tích tĩnh PHPStan:

```bash
composer phpstan
```

Kiểm tra code style:

```bash
composer cs-check
```

Sửa code style:

```bash
composer cs-fix
```

## Yêu cầu

- PHP >= 7.4

## Tuân thủ đặc tả

Thư viện này triển khai:
- **NAPAS QR Switching Technical Specifications v1.5.2**
- **EMVCo QR Code Specification for Payment Systems: Merchant-Presented Mode**

## Giấy phép

Giấy phép MIT. Xem [LICENSE](LICENSE) để biết chi tiết.

## Tác giả

Phát triển bởi [Liopay](https://liopay.vn)

## Đóng góp

Chúng tôi hoan nghênh mọi đóng góp! Vui lòng gửi Pull Request.

## Hỗ trợ

Để báo cáo lỗi và đặt câu hỏi, vui lòng sử dụng [GitHub issue tracker](https://github.com/liopay/vietqr/issues).
