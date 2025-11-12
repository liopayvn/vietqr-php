<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Liopay\VietQR\Helper\{TLVHelper, CRCHelper};
use Liopay\VietQR\ValueObject\{ServiceCode, PointOfInitiation};

echo "=== VietQR Core - Basic Usage Examples ===\n\n";

// Example 1: TLV Encoding
echo "1. TLV Encoding\n";
echo "---------------\n";
$tlv = new TLVHelper();
$encoded = $tlv->encode('59', 'NGO QUOC DAT');
echo "Encoded merchant name: {$encoded}\n";
echo "Breakdown: ID=59, Length=12, Value=NGO QUOC DAT\n\n";

// Example 2: TLV Decoding
echo "2. TLV Decoding\n";
echo "---------------\n";
$decoded = $tlv->decode('5912NGO QUOC DAT');
echo "Decoded: " . print_r($decoded, true) . "\n";

// Example 3: CRC Calculation
echo "3. CRC Checksum (CRC16-CCITT False)\n";
echo "------------------------------------\n";
$crc = new CRCHelper();
$data = '00020101021138480010A000000727013000069704360114101759560025520' .
    '4581253037045802VN5912NGO QUOC DAT6005HANOI62110307NPS68696304';
$checksum = $crc->calculate($data);
echo "Data: " . substr($data, 0, 50) . "...\n";
echo "CRC: {$checksum}\n";
echo "Complete QR: " . $data . $checksum . "\n\n";

// Example 4: CRC Verification
echo "4. CRC Verification\n";
echo "-------------------\n";
$validQR = '00020101021138480010A000000727013000069704360114101759560025520' .
    '4581253037045802VN5912NGO QUOC DAT6005HANOI62110307NPS686963045802';
$isValid = $crc->verify($validQR);
echo "QR String: " . substr($validQR, 0, 50) . "...\n";
echo "Valid: " . ($isValid ? 'YES ✓' : 'NO ✗') . "\n\n";

// Example 5: Value Objects
echo "5. Value Objects\n";
echo "----------------\n";
$serviceCode = new ServiceCode('QRPUSH');
echo "Service: {$serviceCode->getValue()}\n";
echo "Is Payment: " . ($serviceCode->isPayment() ? 'YES' : 'NO') . "\n";
echo "Is IBFT: " . ($serviceCode->isIBFT() ? 'YES' : 'NO') . "\n\n";

$poi = PointOfInitiation::static();
echo "Point of Initiation: {$poi->getValue()}\n";
echo "Is Static: " . ($poi->isStatic() ? 'YES' : 'NO') . "\n";
echo "Is Dynamic: " . ($poi->isDynamic() ? 'YES' : 'NO') . "\n\n";

echo "=== Examples completed successfully! ===\n";
