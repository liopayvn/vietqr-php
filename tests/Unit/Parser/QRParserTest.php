<?php

declare(strict_types=1);

namespace Liopay\VietQR\Tests\Unit\Parser;

use Liopay\VietQR\Builder\{QRPushBuilder, QRCashBuilder, QRIBFTBuilder};
use Liopay\VietQR\Parser\QRParser;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for QRParser
 */
final class QRParserTest extends TestCase
{
    private QRParser $parser;

    protected function setUp(): void
    {
        $this->parser = new QRParser();
    }

    public function testParseQRPushStaticWithoutServiceCode(): void
    {
        // Build a QR code
        $builder = new QRPushBuilder();
        $qr = $builder
            ->setPointOfInitiation('11')
            ->setAcquirerBankBin('970436')
            ->setMerchantId('1017595600')
            ->setMerchantCategoryCode('5812')
            ->setMerchantName('NGO QUOC DAT')
            ->setMerchantCity('HANOI')
            ->build();

        // Parse it
        $parsed = $this->parser->parse($qr);

        // Verify parsed data
        $this->assertSame('01', $parsed->getPayloadFormat());
        $this->assertSame('11', $parsed->getPointOfInitiation());
        $this->assertTrue($parsed->isStatic());
        $this->assertFalse($parsed->isDynamic());
        $this->assertSame('970436', $parsed->getBankBin());
        $this->assertSame('1017595600', $parsed->getMerchantId());
        $this->assertNull($parsed->getServiceCode());
        $this->assertSame('5812', $parsed->getMerchantCategoryCode());
        $this->assertSame('704', $parsed->getCurrency());
        $this->assertSame('VN', $parsed->getCountry());
        $this->assertSame('NGO QUOC DAT', $parsed->getMerchantName());
        $this->assertSame('HANOI', $parsed->getMerchantCity());
        $this->assertSame('QRPUSH', $parsed->getQRType());
    }

    public function testParseQRPushDynamicWithServiceCode(): void
    {
        // Build a QR code
        $builder = new QRPushBuilder();
        $qr = $builder
            ->setPointOfInitiation('12')
            ->setAcquirerBankBin('970436')
            ->setMerchantId('1017595600')
            ->setServiceCode('QRPUSH')
            ->setMerchantCategoryCode('5411')
            ->setAmount('50000')
            ->setMerchantName('NGO QUOC DAT')
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
        $this->assertSame('970436', $parsed->getBankBin());
        $this->assertSame('1017595600', $parsed->getMerchantId());
        $this->assertSame('QRPUSH', $parsed->getServiceCode());
        $this->assertSame('5411', $parsed->getMerchantCategoryCode());
        $this->assertSame('50000', $parsed->getAmount());
        $this->assertSame('NGO QUOC DAT', $parsed->getMerchantName());
        $this->assertSame('HOCHIMINH', $parsed->getMerchantCity());
        $this->assertSame('700000', $parsed->getPostalCode());
        $this->assertSame('REF123', $parsed->getAdditionalData()->getReferenceLabel());
        $this->assertSame('Payment', $parsed->getAdditionalData()->getPurposeOfTransaction());
        $this->assertSame('QRPUSH', $parsed->getQRType());
    }

    public function testParseQRCash(): void
    {
        // Build a QR Cash code
        $builder = new QRCashBuilder();
        $qr = $builder
            ->setPointOfInitiation('12')
            ->setAcquirerBankBin('970436')
            ->setATMId('12345678')
            ->setCashService()
            ->setMerchantCategoryCode('6011')
            ->setMerchantName('NGO QUOC DAT')
            ->setMerchantCity('HANOI')
            ->setReferenceLabel('TRANS001')
            ->setTerminalLabel('TERM001')
            ->build();

        // Parse it
        $parsed = $this->parser->parse($qr);

        // Verify parsed data
        $this->assertSame('12', $parsed->getPointOfInitiation());
        $this->assertSame('970436', $parsed->getBankBin());
        $this->assertSame('12345678', $parsed->getMerchantId());
        $this->assertSame('QRCASH', $parsed->getServiceCode());
        $this->assertSame('6011', $parsed->getMerchantCategoryCode());
        $this->assertSame('NGO QUOC DAT', $parsed->getMerchantName());
        $this->assertSame('HANOI', $parsed->getMerchantCity());
        $this->assertSame('TRANS001', $parsed->getAdditionalData()->getReferenceLabel());
        $this->assertSame('TERM001', $parsed->getAdditionalData()->getTerminalLabel());
        $this->assertSame('QRCASH', $parsed->getQRType());
    }

    public function testParseQRIBFTToAccount(): void
    {
        // Build a QR IBFT code
        $builder = new QRIBFTBuilder();
        $qr = $builder
            ->setPointOfInitiation('11')
            ->setBeneficiaryBankBin('970436')
            ->setConsumerId('1017595600')
            ->setIBFTToAccount()
            ->setAmount('250000')
            ->setReferenceLabel('REF456')
            ->build();

        // Parse it
        $parsed = $this->parser->parse($qr);

        // Verify parsed data
        $this->assertSame('11', $parsed->getPointOfInitiation());
        $this->assertSame('970436', $parsed->getBankBin());
        $this->assertSame('1017595600', $parsed->getMerchantId());
        $this->assertSame('QRIBFTTA', $parsed->getServiceCode());
        $this->assertSame('250000', $parsed->getAmount());
        $this->assertSame('REF456', $parsed->getAdditionalData()->getReferenceLabel());
        $this->assertSame('QRIBFTTA', $parsed->getQRType());
    }

    public function testParseQRIBFTToCard(): void
    {
        // Build a QR IBFT to card code
        $builder = new QRIBFTBuilder();
        $qr = $builder
            ->setPointOfInitiation('11')
            ->setBeneficiaryBankBin('970436')
            ->setConsumerId('9704361017595600')
            ->setIBFTToCard()
            ->build();

        // Parse it
        $parsed = $this->parser->parse($qr);

        // Verify parsed data
        $this->assertSame('11', $parsed->getPointOfInitiation());
        $this->assertSame('970436', $parsed->getBankBin());
        $this->assertSame('9704361017595600', $parsed->getMerchantId());
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
        $builder = new QRPushBuilder();
        $qr = $builder
            ->setPointOfInitiation('12')
            ->setAcquirerBankBin('970436')
            ->setMerchantId('1017595600')
            ->setMerchantCategoryCode('5812')
            ->setAmount('100000')
            ->setMerchantName('NGO QUOC DAT')
            ->setMerchantCity('HN')
            ->setBillNumber('BILL123')
            ->setMobileNumber('0912345678')
            ->setReferenceLabel('REF001')
            ->setCustomerLabel('CUST001')
            ->setPurposeOfTransaction('Payment')
            ->setAdditionalConsumerDataRequest('REQINFO')
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
        $this->assertSame('REQINFO', $adf->getAdditionalConsumerDataRequest());
    }

    public function testRoundTripBuildAndParse(): void
    {
        // Build a comprehensive QR code with all fields
        $builder = (new QRPushBuilder())
            ->setAcquirerBankBin('970422')
            ->setMerchantId('0123456789')
            ->setServiceCode('QRPUSH')
            ->setMerchantCategoryCode('5999')
            ->setCurrency('704')
            ->setAmount('100000.50')
            ->setCountry('vn') // Test lowercase normalization
            ->setMerchantName('Test Merchant')
            ->setMerchantCity('Hanoi')
            ->setPostalCode('100000')
            ->setBillNumber('BILL123')
            ->setMobileNumber('0901234567')
            ->setReferenceLabel('REF001')
            ->setAdditionalConsumerDataRequest('EXTRADATA');

        $qrString = $builder->build();

        // Parse the QR code back
        $parsed = $this->parser->parse($qrString);

        // Verify all fields match
        $this->assertSame('970422', $parsed->getBankBin());
        $this->assertSame('0123456789', $parsed->getMerchantId());
        $this->assertSame('QRPUSH', $parsed->getServiceCode());
        $this->assertSame('5999', $parsed->getMerchantCategoryCode());
        $this->assertSame('704', $parsed->getCurrency());
        $this->assertSame('100000.50', $parsed->getAmount());
        $this->assertSame('VN', $parsed->getCountry()); // Should be uppercase after normalization
        $this->assertSame('Test Merchant', $parsed->getMerchantName());
        $this->assertSame('Hanoi', $parsed->getMerchantCity());
        $this->assertSame('100000', $parsed->getPostalCode());

        // Verify additional data fields
        $adf = $parsed->getAdditionalData();
        $this->assertSame('BILL123', $adf->getBillNumber());
        $this->assertSame('0901234567', $adf->getMobileNumber());
        $this->assertSame('REF001', $adf->getReferenceLabel());
        $this->assertSame('EXTRADATA', $adf->getAdditionalConsumerDataRequest());
    }
}
