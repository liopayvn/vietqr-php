<?php

declare(strict_types=1);

namespace Liopay\VietQR\Tests\Integration;

use Liopay\VietQR\Builder\QRCashBuilder;
use Liopay\VietQR\Helper\CRCHelper;
use PHPUnit\Framework\TestCase;

/**
 * Integration tests for QRCashBuilder using specification examples
 *
 * Test cases from NAPAS QR Switching specification v1.5.2, page 33
 */
final class QRCashBuilderIntegrationTest extends TestCase
{
    private QRCashBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new QRCashBuilder();
    }

    /**
     * Test case 6.2 from specification (page 33)
     * QR Cash example
     */
    public function testBuildQRCashFromSpecification(): void
    {
        $qr = $this->builder
            ->setPointOfInitiation('12')  // Dynamic
            ->setAcquirerBankBin('970436')
            ->setATMId('12345678')
            ->setCashService()
            ->setMerchantCategoryCode('6011')
            ->setMerchantName('NGO QUOC DAT')
            ->setMerchantCity('HANOI')
            ->setReferenceLabel('20190109155714228384')
            ->setTerminalLabel('00001111')
            ->build();

        // Verify QR was built and contains expected data
        $this->assertStringContainsString('970436', $qr);
        $this->assertStringContainsString('12345678', $qr);
        $this->assertStringContainsString('QRCASH', $qr);
        $this->assertStringContainsString('NGO QUOC DAT', $qr);
        $this->assertStringContainsString('20190109155714228384', $qr);
        $this->assertStringContainsString('00001111', $qr);

        // Verify CRC
        $crcHelper = new CRCHelper();
        $this->assertTrue($crcHelper->verify($qr));
    }

    /**
     * Test QR Cash with optional amount
     */
    public function testBuildQRCashWithAmount(): void
    {
        $qr = $this->builder
            ->setPointOfInitiation('12')
            ->setAcquirerBankBin('970436')
            ->setATMId('12345678')
            ->setCashService()
            ->setMerchantCategoryCode('6011')
            ->setAmount('500000')
            ->setMerchantName('NGO QUOC DAT')
            ->setMerchantCity('HOCHIMINH')
            ->setReferenceLabel('REF123456')
            ->setTerminalLabel('ATM001')
            ->build();

        // Should contain amount field
        $this->assertStringContainsString('500000', $qr);
        $this->assertStringContainsString('QRCASH', $qr);
        $this->assertStringContainsString('6011', $qr);

        // Verify CRC
        $crcHelper = new CRCHelper();
        $this->assertTrue($crcHelper->verify($qr));
    }

    /**
     * Test QR Cash with postal code
     */
    public function testBuildQRCashWithPostalCode(): void
    {
        $qr = $this->builder
            ->setPointOfInitiation('12')
            ->setAcquirerBankBin('970436')
            ->setATMId('87654321')
            ->setCashService()
            ->setMerchantCategoryCode('6011')
            ->setMerchantName('NGO QUOC DAT')
            ->setMerchantCity('DANANG')
            ->setPostalCode('550000')
            ->setReferenceLabel('TRANS001')
            ->setTerminalLabel('TERM001')
            ->build();

        // Should contain postal code
        $this->assertStringContainsString('550000', $qr);
        $this->assertStringContainsString('QRCASH', $qr);

        // Verify CRC
        $crcHelper = new CRCHelper();
        $this->assertTrue($crcHelper->verify($qr));
    }

    /**
     * Test missing required reference label throws exception
     */
    public function testMissingReferenceLabelThrowsException(): void
    {
        $this->expectException(\Liopay\VietQR\Exception\MissingRequiredFieldException::class);
        $this->expectExceptionMessage('Reference Label is required');

        $this->builder
            ->setPointOfInitiation('12')
            ->setAcquirerBankBin('970436')
            ->setATMId('12345678')
            ->setCashService()
            ->setMerchantCategoryCode('6011')
            ->setMerchantName('NGO QUOC DAT')
            ->setMerchantCity('HANOI')
            ->setTerminalLabel('TERM001')
            ->build();
    }

    /**
     * Test missing required terminal label throws exception
     */
    public function testMissingTerminalLabelThrowsException(): void
    {
        $this->expectException(\Liopay\VietQR\Exception\MissingRequiredFieldException::class);
        $this->expectExceptionMessage('Terminal Label is required');

        $this->builder
            ->setPointOfInitiation('12')
            ->setAcquirerBankBin('970436')
            ->setATMId('12345678')
            ->setCashService()
            ->setMerchantCategoryCode('6011')
            ->setMerchantName('NGO QUOC DAT')
            ->setMerchantCity('HANOI')
            ->setReferenceLabel('REF123')
            ->build();
    }

    /**
     * Test builder reset functionality
     */
    public function testBuilderReset(): void
    {
        // Build first QR
        $qr1 = $this->builder
            ->setPointOfInitiation('12')
            ->setAcquirerBankBin('970436')
            ->setATMId('12345678')
            ->setCashService()
            ->setMerchantCategoryCode('6011')
            ->setMerchantName('NGO QUOC DAT')
            ->setMerchantCity('HANOI')
            ->setReferenceLabel('REF001')
            ->setTerminalLabel('TERM001')
            ->build();

        // Reset and build second QR
        $qr2 = $this->builder
            ->reset()
            ->setPointOfInitiation('12')
            ->setAcquirerBankBin('970436')
            ->setATMId('87654321')
            ->setCashService()
            ->setMerchantCategoryCode('6011')
            ->setMerchantName('NGO QUOC DAT')
            ->setMerchantCity('HOCHIMINH')
            ->setReferenceLabel('REF002')
            ->setTerminalLabel('TERM002')
            ->build();

        // Should be different
        $this->assertNotSame($qr1, $qr2);

        // Both should have valid CRC
        $crcHelper = new CRCHelper();
        $this->assertTrue($crcHelper->verify($qr1));
        $this->assertTrue($crcHelper->verify($qr2));
    }
}
