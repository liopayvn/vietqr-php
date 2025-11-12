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
            ->setAcquirerBankBin('970403')
            ->setMerchantId('2112995044604025')
            ->setMerchantCategoryCode('5812')
            ->setMerchantName('PHUONG CAC')
            ->setMerchantCity('HANOI')
            ->setStoreLabel('NPS6869')
            ->build();

        // Expected from specification
        $expected = '00020101021138480010A00000072701300006970403011621129950446040255204'
            . '581253037045802VN5910PHUONG CAC6005HANOI62110307NPS686963045802';

        $this->assertSame($expected, $qr);

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
            ->setAcquirerBankBin('970403')
            ->setMerchantId('2112995044604025')
            ->setServiceCode('QRPUSH')
            ->setMerchantCategoryCode('5812')
            ->setMerchantName('PHUONG CAC')
            ->setMerchantCity('HANOI')
            ->setStoreLabel('NPS6869')
            ->build();

        // Expected from specification
        $expected = '00020101021138580010A00000072701300006970403011621129950446040250206'
            . 'QRPUSH5204581253037045802VN5910PHUONG CAC6005HANOI62110307NPS686963043820';

        $this->assertSame($expected, $qr);

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
            ->setAcquirerBankBin('970403')
            ->setMerchantId('2112995044604025')
            ->setMerchantCategoryCode('5812')
            ->setAmount('180000')
            ->setMerchantName('PHUONG CAC')
            ->setMerchantCity('HANOI')
            ->setStoreLabel('NPS6869')
            ->build();

        // Expected from specification (PDF has typo - missing '2' in country code length)
        $expected = '00020101021238480010A00000072701300006970403011621129950446040255204'
            . '581253037045406180000' . '5802VN5910PHUONG CAC6005HANOI62110307NPS6869630479EE';

        $this->assertSame($expected, $qr);

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
            ->setAcquirerBankBin('970403')
            ->setMerchantId('2112995044604025')
            ->setServiceCode('QRPUSH')
            ->setMerchantCategoryCode('5812')
            ->setAmount('180000')
            ->setMerchantName('PHUONG CAC')
            ->setMerchantCity('HANOI')
            ->setStoreLabel('NPS6869')
            ->build();

        // Expected from specification (PDF has typo - missing '2' in country code length)
        $expected = '00020101021238580010A00000072701300006970403011621129950446040250206'
            . 'QRPUSH5204581253037045406180000' . '5802VN5910PHUONG CAC6005HANOI62110307NPS686963047C1B';

        $this->assertSame($expected, $qr);

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
            ->setAcquirerBankBin('970403')
            ->setMerchantId('211299504460425')
            ->setMerchantCategoryCode('5812')
            ->setAmount('180000.50')
            ->setMerchantName('PHUONG CAC')
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
            ->setAcquirerBankBin('970403')
            ->setMerchantId('211299504460425')
            ->setMerchantCategoryCode('5812')
            ->setMerchantName('PHUONG CAC')
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
            ->setAcquirerBankBin('970403')
            ->setMerchantId('211299504460425')
            ->setMerchantCategoryCode('5812')
            ->setMerchantName('PHUONG CAC')
            ->setMerchantCity('HANOI')
            ->build();

        // Reset and build second QR
        $qr2 = $this->builder
            ->reset()
            ->setPointOfInitiation('11')
            ->setAcquirerBankBin('970468')
            ->setMerchantId('123456789')
            ->setMerchantCategoryCode('5411')
            ->setMerchantName('TEST MERCHANT')
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
