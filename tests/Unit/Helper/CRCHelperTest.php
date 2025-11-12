<?php

declare(strict_types=1);

namespace Liopay\VietQR\Tests\Unit\Helper;

use Liopay\VietQR\Helper\CRCHelper;
use PHPUnit\Framework\TestCase;

final class CRCHelperTest extends TestCase
{
    private CRCHelper $crcHelper;

    protected function setUp(): void
    {
        $this->crcHelper = new CRCHelper();
    }

    /**
     * Test CRC calculation with example from specification (page 28)
     * Static QR PUSH without service code
     */
    public function testCalculateCRCFromSpecificationExample(): void
    {
        // Data from specification example 6.1.1 (page 28) without CRC
        $dataWithoutCRC = '00020101021138480010A00000072701300006970403011621129950446040255204'
            . '581253037045802VN5910PHUONG CAC6005HANOI62110307NPS68696304';

        $expectedCRC = '5802';
        $actualCRC = $this->crcHelper->calculate($dataWithoutCRC);

        $this->assertSame($expectedCRC, $actualCRC);
    }

    /**
     * Test verify with complete QR string from specification
     */
    public function testVerifyValidQRString(): void
    {
        // Complete QR string from specification example 6.1.1
        $validQR = '00020101021138480010A00000072701300006970403011621129950446040255204'
            . '581253037045802VN5910PHUONG CAC6005HANOI62110307NPS686963045802';

        $this->assertTrue($this->crcHelper->verify($validQR));
    }

    /**
     * Test verify with invalid CRC
     */
    public function testVerifyInvalidCRC(): void
    {
        // QR string with incorrect CRC (changed last digit)
        $invalidQR = '00020101021138480010A00000072701300006970403011621129950446040255204'
            . '581253037045802VN5910PHUONG CAC6005HANOI62110307NPS686963045803';

        $this->assertFalse($this->crcHelper->verify($invalidQR));
    }

    /**
     * Test append CRC to QR data
     */
    public function testAppendCRC(): void
    {
        $dataWithoutCRC = '00020101021138480010A00000072701300006970403011621129950446040255204'
            . '581253037045802VN5910PHUONG CAC6005HANOI62110307NPS6869';

        $result = $this->crcHelper->append($dataWithoutCRC);

        // Should end with 63045802
        $this->assertStringEndsWith('5802', $result);
        $this->assertTrue($this->crcHelper->verify($result));
    }

    /**
     * Test CRC is always 4 characters uppercase hex
     */
    public function testCRCFormat(): void
    {
        $crc = $this->crcHelper->calculate('test data');

        $this->assertMatchesRegularExpression('/^[0-9A-F]{4}$/', $crc);
        $this->assertSame(4, strlen($crc));
    }
}
