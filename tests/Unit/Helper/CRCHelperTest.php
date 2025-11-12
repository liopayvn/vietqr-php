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
        $dataWithoutCRC = '00020101021138480010A000000727013000069704360114101759560025520'
            . '4581253037045802VN5912NGO QUOC DAT6005HANOI62110307NPS68696304';

        $actualCRC = $this->crcHelper->calculate($dataWithoutCRC);

        // Verify CRC is valid format (4 character uppercase hex)
        $this->assertMatchesRegularExpression('/^[0-9A-F]{4}$/', $actualCRC);
    }

    /**
     * Test verify with complete QR string from specification
     */
    public function testVerifyValidQRString(): void
    {
        // Build a valid QR string with correct CRC
        $dataWithoutCRC = '00020101021138480010A000000727013000069704360114101759560025520'
            . '4581253037045802VN5912NGO QUOC DAT6005HANOI62110307NPS68696304';
        $validQR = $this->crcHelper->append($dataWithoutCRC);

        $this->assertTrue($this->crcHelper->verify($validQR));
    }

    /**
     * Test verify with invalid CRC
     */
    public function testVerifyInvalidCRC(): void
    {
        // Build a QR string then corrupt the CRC
        $dataWithoutCRC = '00020101021138480010A000000727013000069704360114101759560025520'
            . '4581253037045802VN5912NGO QUOC DAT6005HANOI62110307NPS68696304';
        $validQR = $this->crcHelper->append($dataWithoutCRC);

        // Corrupt the last digit of CRC
        $invalidQR = substr($validQR, 0, -1) . '0';

        $this->assertFalse($this->crcHelper->verify($invalidQR));
    }

    /**
     * Test append CRC to QR data
     */
    public function testAppendCRC(): void
    {
        $dataWithoutCRC = '00020101021138480010A000000727013000069704360114101759560025520'
            . '4581253037045802VN5912NGO QUOC DAT6005HANOI62110307NPS6869';

        $result = $this->crcHelper->append($dataWithoutCRC);

        // Should end with 6304 followed by 4-char CRC
        $this->assertStringContainsString('6304', $result);
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
