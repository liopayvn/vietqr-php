<?php

declare(strict_types=1);

namespace Liopay\VietQR\Helper;

/**
 * CRC16-CCITT False calculator for VietQR
 *
 * Implements CRC16-CCITT False algorithm with:
 * - Polynomial: 0x1021
 * - Initial value: 0xFFFF
 * - Applied to all QR data including "6304" but excluding the CRC value itself
 *
 * @package Liopay\VietQR\Helper
 */
final class CRCHelper
{
    private const POLYNOMIAL = 0x1021;
    private const INIT_VALUE = 0xFFFF;
    private const CRC_TAG = '6304';

    /**
     * Calculate CRC16-CCITT False checksum
     *
     * @param string $data Data to calculate CRC for
     * @return string 4-character hexadecimal CRC (uppercase)
     */
    public function calculate(string $data): string
    {
        $crc = self::INIT_VALUE;
        $length = strlen($data);

        for ($i = 0; $i < $length; $i++) {
            $crc ^= (ord($data[$i]) << 8);

            for ($j = 0; $j < 8; $j++) {
                if (($crc & 0x8000) !== 0) {
                    $crc = (($crc << 1) ^ self::POLYNOMIAL) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return strtoupper(sprintf('%04X', $crc));
    }

    /**
     * Verify CRC of complete QR string
     *
     * @param string $qrString Complete QR string with CRC
     * @return bool True if CRC is valid
     */
    public function verify(string $qrString): bool
    {
        if (strlen($qrString) < 8) { // Need at least 6304 + 4 chars for CRC
            return false;
        }

        // Extract CRC from end of string (last 4 characters)
        $providedCrc = substr($qrString, -4);

        // Get data without CRC value (should already end with "6304")
        $dataWithTag = substr($qrString, 0, -4);

        // Calculate expected CRC
        $calculatedCrc = $this->calculate($dataWithTag);

        return $providedCrc === $calculatedCrc;
    }

    /**
     * Append CRC to QR data
     *
     * @param string $qrData QR data without CRC
     * @return string Complete QR string with CRC
     */
    public function append(string $qrData): string
    {
        // Append CRC tag without value
        $dataWithTag = $qrData . self::CRC_TAG;

        // Calculate CRC
        $crc = $this->calculate($dataWithTag);

        // Return complete QR string
        return $dataWithTag . $crc;
    }
}
