<?php

declare(strict_types=1);

namespace Liopay\VietQR\Tests\Unit\Helper;

use Liopay\VietQR\Helper\TLVHelper;
use Liopay\VietQR\Exception\InvalidFormatException;
use PHPUnit\Framework\TestCase;

final class TLVHelperTest extends TestCase
{
    private TLVHelper $tlvHelper;

    protected function setUp(): void
    {
        $this->tlvHelper = new TLVHelper();
    }

    public function testEncodeSimpleValue(): void
    {
        $result = $this->tlvHelper->encode('00', '01');

        $this->assertSame('000201', $result);
    }

    public function testEncodeMerchantName(): void
    {
        $result = $this->tlvHelper->encode('59', 'PHUONG CAC');

        $this->assertSame('5910PHUONG CAC', $result);
    }

    public function testDecodeSimpleValue(): void
    {
        $result = $this->tlvHelper->decode('000201');

        $this->assertSame(['00' => '01'], $result);
    }

    public function testDecodeMultipleValues(): void
    {
        // Fixed: Correct TLV format with proper lengths
        $tlvString = '000201' . '5303704';  // 00|02|01 + 53|03|704
        $result = $this->tlvHelper->decode($tlvString);

        $expected = [
            '00' => '01',
            '53' => '704',
        ];

        $this->assertSame($expected, $result);
    }

    public function testDecodeSingle(): void
    {
        $result = $this->tlvHelper->decodeSingle('5910PHUONG CAC', 0);

        $this->assertSame('59', $result['id']);
        $this->assertSame(10, $result['length']);
        $this->assertSame('PHUONG CAC', $result['value']);
        $this->assertSame(14, $result['next']);
    }

    public function testEncodeInvalidIdThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid TLV ID');

        $this->tlvHelper->encode('0', 'value');
    }

    public function testEncodeEmptyValueThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('TLV value cannot be empty');

        $this->tlvHelper->encode('00', '');
    }

    public function testEncodeTooLongValueThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('exceeds maximum of 99 bytes');

        $this->tlvHelper->encode('00', str_repeat('x', 100));
    }

    public function testDecodeInvalidFormatThrowsException(): void
    {
        $this->expectException(InvalidFormatException::class);

        $this->tlvHelper->decode('00'); // Too short
    }

    public function testDecodeInvalidLengthThrowsException(): void
    {
        $this->expectException(InvalidFormatException::class);

        // Length says 10 but only 5 characters provided
        $this->tlvHelper->decode('001012345');
    }

    public function testValidateValidTLV(): void
    {
        $this->assertTrue($this->tlvHelper->validate('000201'));
        $this->assertTrue($this->tlvHelper->validate('5910PHUONG CAC'));
    }

    public function testValidateInvalidTLV(): void
    {
        $this->assertFalse($this->tlvHelper->validate('00')); // Too short
        $this->assertFalse($this->tlvHelper->validate('001012345')); // Invalid length
    }
}
