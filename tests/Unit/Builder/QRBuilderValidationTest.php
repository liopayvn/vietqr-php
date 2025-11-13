<?php

declare(strict_types=1);

namespace Liopay\VietQR\Tests\Unit\Builder;

use Liopay\VietQR\Builder\QRPushBuilder;
use Liopay\VietQR\Exception\{InvalidLengthException, ValidationException};
use PHPUnit\Framework\TestCase;

final class QRBuilderValidationTest extends TestCase
{
    public function testSetServiceCodeRejectsUnknownValue(): void
    {
        $builder = new QRPushBuilder();

        $this->expectException(ValidationException::class);
        $builder->setServiceCode('INVALID');
    }

    public function testSetServiceCodeAcceptsValidValues(): void
    {
        $builder = new QRPushBuilder();

        // Test all valid service codes
        $builder->setServiceCode('QRPUSH');
        $builder->setServiceCode('QRCASH');
        $builder->setServiceCode('QRIBFTTC');
        $builder->setServiceCode('QRIBFTTA');

        // If we get here without exceptions, the test passes
        $this->assertTrue(true);
    }

    public function testSetCurrencyRequiresThreeDigits(): void
    {
        $builder = new QRPushBuilder();

        $this->expectException(ValidationException::class);
        $builder->setCurrency('70A');
    }

    public function testSetCurrencyRequiresExactLength(): void
    {
        $builder = new QRPushBuilder();

        $this->expectException(InvalidLengthException::class);
        $builder->setCurrency('70');
    }

    public function testSetCountryNormalizesToUppercase(): void
    {
        $builder = new QRPushBuilder();

        // Lowercase input should be normalized to uppercase
        $builder->setCountry('vn');

        // Build to verify it works (would throw if validation failed)
        $qr = $builder
            ->setAcquirerBankBin('970422')
            ->setMerchantId('0123456789')
            ->setMerchantCategoryCode('5999')
            ->setMerchantName('Test Merchant')
            ->setMerchantCity('Hanoi')
            ->build();

        // Verify the country code is in the QR string as uppercase
        $this->assertStringContainsString('5802VN', $qr);
    }

    public function testSetCurrencyAcceptsValidNumericCode(): void
    {
        $builder = new QRPushBuilder();

        // Should accept valid 3-digit numeric currency code
        $builder->setCurrency('704'); // VND
        $builder->setCurrency('840'); // USD

        // If we get here without exceptions, the test passes
        $this->assertTrue(true);
    }

    public function testSetCountryAcceptsValidAlphaCode(): void
    {
        $builder = new QRPushBuilder();

        // Should accept valid 2-letter country codes
        $builder->setCountry('VN');
        $builder->setCountry('US');
        $builder->setCountry('vn'); // Also lowercase (will be normalized)

        // If we get here without exceptions, the test passes
        $this->assertTrue(true);
    }
}
