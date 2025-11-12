<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Liopay\VietQR\Builder\{QRPushBuilder, QRCashBuilder, QRIBFTBuilder};
use Liopay\VietQR\Parser\QRParser;

echo "=== VietQR Parser Usage Examples ===" . PHP_EOL . PHP_EOL;

// Example 1: Build and Parse a QR Push Payment
echo "Example 1: QR Push Payment" . PHP_EOL;
echo str_repeat("-", 50) . PHP_EOL;

$pushBuilder = new QRPushBuilder();
$qrPush = $pushBuilder
    ->setPointOfInitiation('12')  // Dynamic QR
    ->setAcquirerBankBin('970436')
    ->setMerchantId('1017595600')
    ->setServiceCode('QRPUSH')
    ->setMerchantCategoryCode('5812')
    ->setAmount('50000')
    ->setMerchantName('NGO QUOC DAT')
    ->setMerchantCity('HANOI')
    ->setReferenceLabel('ORDER123')
    ->setPurposeOfTransaction('Coffee payment')
    ->build();

echo "Generated QR: " . $qrPush . PHP_EOL . PHP_EOL;

// Parse the QR code
$parser = new QRParser();
$parsed = $parser->parse($qrPush);

echo "Parsed Data:" . PHP_EOL;
echo "  QR Type: " . $parsed->getQRType() . PHP_EOL;
echo "  Bank BIN: " . $parsed->getBankBin() . PHP_EOL;
echo "  Merchant ID: " . $parsed->getMerchantId() . PHP_EOL;
echo "  Merchant Name: " . $parsed->getMerchantName() . PHP_EOL;
echo "  Amount: " . $parsed->getAmount() . " VND" . PHP_EOL;
echo "  Reference: " . $parsed->getAdditionalData()->getReferenceLabel() . PHP_EOL;
echo "  Purpose: " . $parsed->getAdditionalData()->getPurposeOfTransaction() . PHP_EOL;
echo "  Is Dynamic: " . ($parsed->isDynamic() ? 'Yes' : 'No') . PHP_EOL;
echo PHP_EOL;

// Example 2: Build and Parse a QR Cash Withdrawal
echo "Example 2: QR Cash Withdrawal" . PHP_EOL;
echo str_repeat("-", 50) . PHP_EOL;

$cashBuilder = new QRCashBuilder();
$qrCash = $cashBuilder
    ->setPointOfInitiation('12')
    ->setAcquirerBankBin('970436')
    ->setATMId('ATM001')
    ->setCashService()
    ->setMerchantCategoryCode('6011')
    ->setMerchantName('NGO QUOC DAT')
    ->setMerchantCity('HOCHIMINH')
    ->setReferenceLabel('TRANS20240101123456')
    ->setTerminalLabel('TERM001')
    ->build();

echo "Generated QR: " . $qrCash . PHP_EOL . PHP_EOL;

$parsedCash = $parser->parse($qrCash);

echo "Parsed Data:" . PHP_EOL;
echo "  QR Type: " . $parsedCash->getQRType() . PHP_EOL;
echo "  Bank BIN: " . $parsedCash->getBankBin() . PHP_EOL;
echo "  ATM ID: " . $parsedCash->getMerchantId() . PHP_EOL;
echo "  Location: " . $parsedCash->getMerchantName() . ", " . $parsedCash->getMerchantCity() . PHP_EOL;
echo "  Reference: " . $parsedCash->getAdditionalData()->getReferenceLabel() . PHP_EOL;
echo "  Terminal: " . $parsedCash->getAdditionalData()->getTerminalLabel() . PHP_EOL;
echo PHP_EOL;

// Example 3: Build and Parse a QR IBFT (Inter-Bank Fund Transfer)
echo "Example 3: QR IBFT to Account" . PHP_EOL;
echo str_repeat("-", 50) . PHP_EOL;

$ibftBuilder = new QRIBFTBuilder();
$qrIbft = $ibftBuilder
    ->setPointOfInitiation('12')
    ->setBeneficiaryBankBin('970436')
    ->setConsumerId('1017595600')
    ->setIBFTToAccount()
    ->setAmount('1000000')
    ->setReferenceLabel('TRANSFER001')
    ->setPurposeOfTransaction('Salary payment')
    ->build();

echo "Generated QR: " . $qrIbft . PHP_EOL . PHP_EOL;

$parsedIbft = $parser->parse($qrIbft);

echo "Parsed Data:" . PHP_EOL;
echo "  QR Type: " . $parsedIbft->getQRType() . PHP_EOL;
echo "  Beneficiary Bank: " . $parsedIbft->getBankBin() . PHP_EOL;
echo "  Account Number: " . $parsedIbft->getMerchantId() . PHP_EOL;
echo "  Amount: " . $parsedIbft->getAmount() . " VND" . PHP_EOL;
echo "  Purpose: " . $parsedIbft->getAdditionalData()->getPurposeOfTransaction() . PHP_EOL;
echo PHP_EOL;

// Example 4: Parse a QR string directly
echo "Example 4: Parse Existing QR String" . PHP_EOL;
echo str_repeat("-", 50) . PHP_EOL;

// This is a valid QR PUSH from the NAPAS specification (page 28)
$existingQR = '00020101021138480010A000000727013000069704360114101759560025520' .
    '4581253037045802VN5912NGO QUOC DAT6005HANOI62110307NPS686963045802';

echo "Input QR: " . $existingQR . PHP_EOL . PHP_EOL;

$parsedExisting = $parser->parse($existingQR);

echo "Parsed Data:" . PHP_EOL;
echo "  QR Type: " . $parsedExisting->getQRType() . PHP_EOL;
echo "  Merchant Name: " . $parsedExisting->getMerchantName() . PHP_EOL;
echo "  City: " . $parsedExisting->getMerchantCity() . PHP_EOL;
echo "  Is Static: " . ($parsedExisting->isStatic() ? 'Yes' : 'No') . PHP_EOL;
echo "  CRC Valid: Yes (verified during parsing)" . PHP_EOL;
echo PHP_EOL;

echo "=== All Examples Completed Successfully ===" . PHP_EOL;
