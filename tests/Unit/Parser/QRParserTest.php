<?php

declare(strict_types=1);

namespace Liopay\VietQR\Tests\Unit\Parser;

use Liopay\VietQR\Builder\{QRPushBuilder, QRCashBuilder, QRIBFTBuilder};
use Liopay\VietQR\Helper\{TLVHelper, CRCHelper};
use Liopay\VietQR\Parser\QRParser;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for QRParser
 */
final class QRParserTest extends TestCase
{
    private QRParser $parser;
    private TLVHelper $tlvHelper;
    private CRCHelper $crcHelper;

    protected function setUp(): void
    {
        $this->tlvHelper = new TLVHelper();
        $this->crcHelper = new CRCHelper();
        $this->parser = new QRParser($this->tlvHelper, $this->crcHelper);
    }

    public function testParseQRPushStaticWithoutServiceCode(): void
    {
        // Build a QR code
        $builder = new QRPushBuilder($this->tlvHelper, $this->crcHelper);
        $qr = $builder
            ->setPointOfInitiation('11')
            ->setAcquirerBankBin('970403')
            ->setMerchantId('2112995044604025')
            ->setMerchantCategoryCode('5812')
            ->setMerchantName('PHUONG CAC')
            ->setMerchantCity('HANOI')
            ->build();

        // Parse it
        $parsed = $this->parser->parse($qr);

        // Verify parsed data
        $this->assertSame('01', $parsed->getPayloadFormat());
        $this->assertSame('11', $parsed->getPointOfInitiation());
        $this->assertTrue($parsed->isStatic());
        $this->assertFalse($parsed->isDynamic());
        $this->assertSame('970403', $parsed->getBankBin());
        $this->assertSame('2112995044604025', $parsed->getMerchantId());
        $this->assertNull($parsed->getServiceCode());
        $this->assertSame('5812', $parsed->getMerchantCategoryCode());
        $this->assertSame('704', $parsed->getCurrency());
        $this->assertSame('VN', $parsed->getCountry());
        $this->assertSame('PHUONG CAC', $parsed->getMerchantName());
        $this->assertSame('HANOI', $parsed->getMerchantCity());
        $this->assertSame('QRPUSH', $parsed->getQRType());
    }

    public function testParseQRPushDynamicWithServiceCode(): void
    {
        // Build a QR code
        $builder = new QRPushBuilder($this->tlvHelper, $this->crcHelper);
        $qr = $builder
            ->setPointOfInitiation('12')
            ->setAcquirerBankBin('970468')
            ->setMerchantId('123456789')
            ->setServiceCode('QRPUSH')
            ->setMerchantCategoryCode('5411')
            ->setAmount('50000')
            ->setMerchantName('Test Merchant')
            ->setMerchantCity('HOCHIMINH')
            ->setPostalCode('700000')
            ->setReferenceLabel('REF123')
            ->setPurposeOfTransaction('Payment')
            ->build();

        // Parse it
        $parsed = $this->parser->parse($qr);

        // Verify parsed data
        $this->assertSame('12', $parsed->getPointOfInitiation());
        $this->assertTrue($parsed->isDynamic());
        $this->assertSame('970468', $parsed->getBankBin());
        $this->assertSame('123456789', $parsed->getMerchantId());
        $this->assertSame('QRPUSH', $parsed->getServiceCode());
        $this->assertSame('5411', $parsed->getMerchantCategoryCode());
        $this->assertSame('50000', $parsed->getAmount());
        $this->assertSame('Test Merchant', $parsed->getMerchantName());
        $this->assertSame('HOCHIMINH', $parsed->getMerchantCity());
        $this->assertSame('700000', $parsed->getPostalCode());
        $this->assertSame('REF123', $parsed->getAdditionalData()->getReferenceLabel());
        $this->assertSame('Payment', $parsed->getAdditionalData()->getPurposeOfTransaction());
        $this->assertSame('QRPUSH', $parsed->getQRType());
    }

    public function testParseQRCash(): void
    {
        // Build a QR Cash code
        $builder = new QRCashBuilder($this->tlvHelper, $this->crcHelper);
        $qr = $builder
            ->setPointOfInitiation('12')
            ->setAcquirerBankBin('970403')
            ->setATMId('12345678')
            ->setCashService()
            ->setMerchantCategoryCode('6011')
            ->setMerchantName('ATM TEST')
            ->setMerchantCity('HANOI')
            ->setReferenceLabel('TRANS001')
            ->setTerminalLabel('TERM001')
            ->build();

        // Parse it
        $parsed = $this->parser->parse($qr);

        // Verify parsed data
        $this->assertSame('12', $parsed->getPointOfInitiation());
        $this->assertSame('970403', $parsed->getBankBin());
        $this->assertSame('12345678', $parsed->getMerchantId());
        $this->assertSame('QRCASH', $parsed->getServiceCode());
        $this->assertSame('6011', $parsed->getMerchantCategoryCode());
        $this->assertSame('ATM TEST', $parsed->getMerchantName());
        $this->assertSame('HANOI', $parsed->getMerchantCity());
        $this->assertSame('TRANS001', $parsed->getAdditionalData()->getReferenceLabel());
        $this->assertSame('TERM001', $parsed->getAdditionalData()->getTerminalLabel());
        $this->assertSame('QRCASH', $parsed->getQRType());
    }

    public function testParseQRIBFTToAccount(): void
    {
        // Build a QR IBFT code
        $builder = new QRIBFTBuilder($this->tlvHelper, $this->crcHelper);
        $qr = $builder
            ->setPointOfInitiation('11')
            ->setBeneficiaryBankBin('970468')
            ->setConsumerId('0011009950446')
            ->setIBFTToAccount()
            ->setAmount('250000')
            ->setReferenceLabel('REF456')
            ->build();

        // Parse it
        $parsed = $this->parser->parse($qr);

        // Verify parsed data
        $this->assertSame('11', $parsed->getPointOfInitiation());
        $this->assertSame('970468', $parsed->getBankBin());
        $this->assertSame('0011009950446', $parsed->getMerchantId());
        $this->assertSame('QRIBFTTA', $parsed->getServiceCode());
        $this->assertSame('250000', $parsed->getAmount());
        $this->assertSame('REF456', $parsed->getAdditionalData()->getReferenceLabel());
        $this->assertSame('QRIBFTTA', $parsed->getQRType());
    }

    public function testParseQRIBFTToCard(): void
    {
        // Build a QR IBFT to card code
        $builder = new QRIBFTBuilder($this->tlvHelper, $this->crcHelper);
        $qr = $builder
            ->setPointOfInitiation('11')
            ->setBeneficiaryBankBin('970403')
            ->setConsumerId('9704031101234567')
            ->setIBFTToCard()
            ->build();

        // Parse it
        $parsed = $this->parser->parse($qr);

        // Verify parsed data
        $this->assertSame('11', $parsed->getPointOfInitiation());
        $this->assertSame('970403', $parsed->getBankBin());
        $this->assertSame('9704031101234567', $parsed->getMerchantId());
        $this->assertSame('QRIBFTTC', $parsed->getServiceCode());
        $this->assertSame('QRIBFTTC', $parsed->getQRType());
    }

    public function testParseInvalidCRCThrowsException(): void
    {
        $this->expectException(\Liopay\VietQR\Exception\InvalidCRCException::class);

        // Create QR with invalid CRC
        $qr = '00020101021138470010A00000072701290006970403011521129950446042552045812'
            . '53037045802VN5910PHUONG CAC6005HANOI62110507NPS686963040000';

        $this->parser->parse($qr);
    }

    public function testParseWithCRCVerificationDisabled(): void
    {
        // Create QR with invalid CRC
        $qr = '00020101021138470010A00000072701290006970403011521129950446042552045812'
            . '53037045802VN5910PHUONG CAC6005HANOI62110507NPS686963040000';

        // Should not throw exception when CRC verification is disabled
        $parsed = $this->parser->parse($qr, false);

        // Should still parse successfully
        $this->assertSame('01', $parsed->getPayloadFormat());
        $this->assertSame('11', $parsed->getPointOfInitiation());
    }

    public function testParseAdditionalDataFields(): void
    {
        // Build QR with several additional data fields (but not so many to exceed 99 byte limit)
        $builder = new QRPushBuilder($this->tlvHelper, $this->crcHelper);
        $qr = $builder
            ->setPointOfInitiation('12')
            ->setAcquirerBankBin('970403')
            ->setMerchantId('123456789')
            ->setMerchantCategoryCode('5812')
            ->setAmount('100000')
            ->setMerchantName('Test')
            ->setMerchantCity('HN')
            ->setBillNumber('BILL123')
            ->setMobileNumber('0912345678')
            ->setReferenceLabel('REF001')
            ->setCustomerLabel('CUST001')
            ->setPurposeOfTransaction('Payment')
            ->build();

        // Parse it
        $parsed = $this->parser->parse($qr);
        $adf = $parsed->getAdditionalData();

        // Verify additional data fields
        $this->assertSame('BILL123', $adf->getBillNumber());
        $this->assertSame('0912345678', $adf->getMobileNumber());
        $this->assertSame('REF001', $adf->getReferenceLabel());
        $this->assertSame('CUST001', $adf->getCustomerLabel());
        $this->assertSame('Payment', $adf->getPurposeOfTransaction());
    }
}
