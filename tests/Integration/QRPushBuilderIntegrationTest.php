<?php

declare(strict_types=1);

namespace Liopay\VietQR\Tests\Integration;

use Liopay\VietQR\Builder\QRPushBuilder;
use Liopay\VietQR\Helper\{TLVHelper, CRCHelper};
use PHPUnit\Framework\TestCase;

/**
 * Integration tests for QRPushBuilder using specification examples
 *
 * Test cases from NAPAS QR Switching specification v1.5.2, pages 28-32
 */
final class QRPushBuilderIntegrationTest extends TestCase
{
    private QRPushBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new QRPushBuilder(new TLVHelper(), new CRCHelper());
    }

    /**
     * Test case 6.1.1 from specification (page 28)
     * Static QR with no service code
     */
    public function testBuildStaticQRPushWithoutServiceCode(): void
    {
        $qr = $this->builder
            ->setPointOfInitiation('11')  // Static
            ->setAcquirerBankBin('970436')
            ->setMerchantId('1017595600')
            ->setMerchantCategoryCode('5812')
            ->setMerchantName('NGO QUOC DAT')
            ->setMerchantCity('HANOI')
            ->setStoreLabel('NPS6869')
            ->build();

        // Verify QR was built and contains expected data
        $this->assertStringContainsString('970436', $qr);
        $this->assertStringContainsString('1017595600', $qr);
        $this->assertStringContainsString('NGO QUOC DAT', $qr);

        // Verify CRC is correct
        $crcHelper = new CRCHelper();
        $this->assertTrue($crcHelper->verify($qr));
    }

    /**
     * Test case 6.1.2 from specification (page 29)
     * Static QR with service code
     */
    public function testBuildStaticQRPushWithServiceCode(): void
    {
        $qr = $this->builder
            ->setPointOfInitiation('11')  // Static
            ->setAcquirerBankBin('970436')
            ->setMerchantId('1017595600')
            ->setServiceCode('QRPUSH')
            ->setMerchantCategoryCode('5812')
            ->setMerchantName('NGO QUOC DAT')
            ->setMerchantCity('HANOI')
            ->setStoreLabel('NPS6869')
            ->build();

        // Verify QR was built and contains expected data
        $this->assertStringContainsString('970436', $qr);
        $this->assertStringContainsString('1017595600', $qr);
        $this->assertStringContainsString('QRPUSH', $qr);
        $this->assertStringContainsString('NGO QUOC DAT', $qr);

        $crcHelper = new CRCHelper();
        $this->assertTrue($crcHelper->verify($qr));
    }

    /**
     * Test case 6.1.3 from specification (page 30)
     * Dynamic QR with no service code
     */
    public function testBuildDynamicQRPushWithAmount(): void
    {
        $qr = $this->builder
            ->setPointOfInitiation('12')  // Dynamic
            ->setAcquirerBankBin('970436')
            ->setMerchantId('1017595600')
            ->setMerchantCategoryCode('5812')
            ->setAmount('180000')
            ->setMerchantName('NGO QUOC DAT')
            ->setMerchantCity('HANOI')
            ->setStoreLabel('NPS6869')
            ->build();

        // Verify QR was built and contains expected data
        $this->assertStringContainsString('970436', $qr);
        $this->assertStringContainsString('1017595600', $qr);
        $this->assertStringContainsString('180000', $qr);
        $this->assertStringContainsString('NGO QUOC DAT', $qr);

        $crcHelper = new CRCHelper();
        $this->assertTrue($crcHelper->verify($qr));
    }

    /**
     * Test case 6.1.4 from specification (page 31)
     * Dynamic QR with service code
     */
    public function testBuildDynamicQRPushWithServiceCodeAndAmount(): void
    {
        $qr = $this->builder
            ->setPointOfInitiation('12')  // Dynamic
            ->setAcquirerBankBin('970436')
            ->setMerchantId('1017595600')
            ->setServiceCode('QRPUSH')
            ->setMerchantCategoryCode('5812')
            ->setAmount('180000')
            ->setMerchantName('NGO QUOC DAT')
            ->setMerchantCity('HANOI')
            ->setStoreLabel('NPS6869')
            ->build();

        // Verify QR was built and contains expected data
        $this->assertStringContainsString('970436', $qr);
        $this->assertStringContainsString('1017595600', $qr);
        $this->assertStringContainsString('QRPUSH', $qr);
        $this->assertStringContainsString('180000', $qr);
        $this->assertStringContainsString('NGO QUOC DAT', $qr);

        $crcHelper = new CRCHelper();
        $this->assertTrue($crcHelper->verify($qr));
    }

    /**
     * Test QR with decimal amount
     */
    public function testBuildQRWithDecimalAmount(): void
    {
        $qr = $this->builder
            ->setPointOfInitiation('12')
            ->setAcquirerBankBin('970436')
            ->setMerchantId('1017595600')
            ->setMerchantCategoryCode('5812')
            ->setAmount('180000.50')
            ->setMerchantName('NGO QUOC DAT')
            ->setMerchantCity('HANOI')
            ->build();

        // Should contain the decimal amount
        $this->assertStringContainsString('180000.50', $qr);

        $crcHelper = new CRCHelper();
        $this->assertTrue($crcHelper->verify($qr));
    }

    /**
     * Test QR with postal code
     */
    public function testBuildQRWithPostalCode(): void
    {
        $qr = $this->builder
            ->setPointOfInitiation('11')
            ->setAcquirerBankBin('970436')
            ->setMerchantId('1017595600')
            ->setMerchantCategoryCode('5812')
            ->setMerchantName('NGO QUOC DAT')
            ->setMerchantCity('HANOI')
            ->setPostalCode('100000')
            ->build();

        // Should contain postal code field (ID 61)
        $this->assertStringContainsString('100000', $qr);

        $crcHelper = new CRCHelper();
        $this->assertTrue($crcHelper->verify($qr));
    }

    /**
     * Test builder reset functionality
     */
    public function testBuilderReset(): void
    {
        // Build first QR
        $qr1 = $this->builder
            ->setPointOfInitiation('11')
            ->setAcquirerBankBin('970436')
            ->setMerchantId('1017595600')
            ->setMerchantCategoryCode('5812')
            ->setMerchantName('NGO QUOC DAT')
            ->setMerchantCity('HANOI')
            ->build();

        // Reset and build second QR
        $qr2 = $this->builder
            ->reset()
            ->setPointOfInitiation('11')
            ->setAcquirerBankBin('970436')
            ->setMerchantId('1017595600')
            ->setMerchantCategoryCode('5411')
            ->setMerchantName('NGO QUOC DAT')
            ->setMerchantCity('HOCHIMINH')
            ->build();

        // Should be different
        $this->assertNotSame($qr1, $qr2);

        // Both should have valid CRC
        $crcHelper = new CRCHelper();
        $this->assertTrue($crcHelper->verify($qr1));
        $this->assertTrue($crcHelper->verify($qr2));
    }
}
