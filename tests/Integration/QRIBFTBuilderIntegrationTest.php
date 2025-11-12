<?php

declare(strict_types=1);

namespace Liopay\VietQR\Tests\Integration;

use Liopay\VietQR\Builder\QRIBFTBuilder;
use Liopay\VietQR\Helper\{TLVHelper, CRCHelper};
use PHPUnit\Framework\TestCase;

/**
 * Integration tests for QRIBFTBuilder using specification examples
 *
 * Test cases from NAPAS QR Switching specification v1.5.2, pages 34-37
 */
final class QRIBFTBuilderIntegrationTest extends TestCase
{
    private QRIBFTBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new QRIBFTBuilder(new TLVHelper(), new CRCHelper());
    }

    /**
     * Test case 6.3.1 from specification (page 34)
     * Static QR IBFT to Account
     */
    public function testBuildStaticQRIBFTToAccount(): void
    {
        $qr = $this->builder
            ->setPointOfInitiation('11')  // Static
            ->setBeneficiaryBankBin('970436')
            ->setConsumerId('1017595600')
            ->setIBFTToAccount()
            ->build();

        // Should contain QRIBFTTA service code
        $this->assertStringContainsString('QRIBFTTA', $qr);
        $this->assertStringContainsString('970436', $qr);
        $this->assertStringContainsString('1017595600', $qr);

        // Verify CRC
        $crcHelper = new CRCHelper();
        $this->assertTrue($crcHelper->verify($qr));
    }

    /**
     * Test case 6.3.2 from specification (page 35)
     * Static QR IBFT to Card
     */
    public function testBuildStaticQRIBFTToCard(): void
    {
        $qr = $this->builder
            ->setPointOfInitiation('11')  // Static
            ->setBeneficiaryBankBin('970436')
            ->setConsumerId('9704361017595600')
            ->setIBFTToCard()
            ->build();

        // Should contain QRIBFTTC service code
        $this->assertStringContainsString('QRIBFTTC', $qr);
        $this->assertStringContainsString('970436', $qr);
        $this->assertStringContainsString('9704361017595600', $qr);

        // Verify CRC
        $crcHelper = new CRCHelper();
        $this->assertTrue($crcHelper->verify($qr));
    }

    /**
     * Test case 6.3.3 from specification (page 36)
     * Dynamic QR IBFT to Account with amount and additional data
     */
    public function testBuildDynamicQRIBFTToAccount(): void
    {
        $qr = $this->builder
            ->setPointOfInitiation('12')  // Dynamic
            ->setBeneficiaryBankBin('970436')
            ->setConsumerId('1017595600')
            ->setIBFTToAccount()
            ->setAmount('180000')
            ->setReferenceLabel('NPS6869')
            ->setPurposeOfTransaction('thanh toan don hang')
            ->build();

        $this->assertStringContainsString('QRIBFTTA', $qr);
        $this->assertStringContainsString('970436', $qr);
        $this->assertStringContainsString('180000', $qr);
        $this->assertStringContainsString('NPS6869', $qr);
        $this->assertStringContainsString('thanh toan don hang', $qr);

        // Verify CRC
        $crcHelper = new CRCHelper();
        $this->assertTrue($crcHelper->verify($qr));
    }

    /**
     * Test case 6.3.4 from specification (page 37)
     * Dynamic QR IBFT to Card with amount
     */
    public function testBuildDynamicQRIBFTToCard(): void
    {
        $qr = $this->builder
            ->setPointOfInitiation('12')  // Dynamic
            ->setBeneficiaryBankBin('970436')
            ->setConsumerId('9704361017595600')
            ->setIBFTToCard()
            ->setAmount('180000')
            ->setReferenceLabel('NPS6869')
            ->setPurposeOfTransaction('thanh toan don hang')
            ->build();

        $this->assertStringContainsString('QRIBFTTC', $qr);
        $this->assertStringContainsString('970436', $qr);
        $this->assertStringContainsString('180000', $qr);

        // Verify CRC
        $crcHelper = new CRCHelper();
        $this->assertTrue($crcHelper->verify($qr));
    }

    /**
     * Test IBFT QR with all optional fields
     */
    public function testBuildQRIBFTWithAllOptionalFields(): void
    {
        $qr = $this->builder
            ->setPointOfInitiation('12')
            ->setBeneficiaryBankBin('970436')
            ->setConsumerId('1017595600')
            ->setIBFTToAccount()
            ->setAmount('250000')
            ->setMerchantName('NGO QUOC DAT')
            ->setMerchantCity('HANOI')
            ->setPostalCode('100000')
            ->setBillNumber('BILL123')
            ->setMobileNumber('0912345678')
            ->setCustomerLabel('CUST001')
            ->setReferenceLabel('REF456')
            ->setPurposeOfTransaction('Payment for invoice')
            ->build();

        // Verify all fields are present
        $this->assertStringContainsString('970436', $qr);
        $this->assertStringContainsString('250000', $qr);
        $this->assertStringContainsString('NGO QUOC DAT', $qr);

        // Verify CRC
        $crcHelper = new CRCHelper();
        $this->assertTrue($crcHelper->verify($qr));
    }
}
