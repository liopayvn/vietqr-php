<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Liopay\VietQR\Builder\QRPushBuilder;
use Liopay\VietQR\Parser\QRParser;
use Liopay\VietQR\ValueObject\{ServiceCode, PointOfInitiation};

echo "=== VietQR - Basic Usage Examples ===\n\n";

// Example 1: Build a Simple QR Code
echo "1. Build a QR Code\n";
echo "------------------\n";
$builder = new QRPushBuilder();
$qr = $builder
    ->setPointOfInitiation('11') // Static QR
    ->setAcquirerBankBin('970436')
    ->setMerchantId('1017595600')
    ->setMerchantCategoryCode('5812')
    ->setMerchantName('NGO QUOC DAT')
    ->setMerchantCity('HANOI')
    ->setReferenceLabel('NPS6869')
    ->build();

echo "Generated QR: " . substr($qr, 0, 50) . "...\n";
echo "Full Length: " . strlen($qr) . " characters\n\n";

// Example 2: Parse a QR Code
echo "2. Parse a QR Code\n";
echo "------------------\n";
$parser = new QRParser();
$parsed = $parser->parse($qr);

echo "Merchant Name: {$parsed->getMerchantName()}\n";
echo "Bank BIN: {$parsed->getBankBin()}\n";
echo "Merchant City: {$parsed->getMerchantCity()}\n";
echo "QR Type: {$parsed->getQRType()}\n";
echo "Is Static: " . ($parsed->isStatic() ? 'YES' : 'NO') . "\n\n";

// Example 3: Dynamic QR with Amount
echo "3. Dynamic QR with Amount\n";
echo "-------------------------\n";
$dynamicQR = (new QRPushBuilder())
    ->setPointOfInitiation('12') // Dynamic
    ->setAcquirerBankBin('970436')
    ->setMerchantId('1017595600')
    ->setMerchantCategoryCode('5812')
    ->setAmount('50000')
    ->setMerchantName('NGO QUOC DAT')
    ->setMerchantCity('HANOI')
    ->build();

$parsedDynamic = $parser->parse($dynamicQR);
echo "Amount: {$parsedDynamic->getAmount()} VND\n";
echo "Is Dynamic: " . ($parsedDynamic->isDynamic() ? 'YES' : 'NO') . "\n\n";

// Example 4: Value Objects
echo "4. Value Objects\n";
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
