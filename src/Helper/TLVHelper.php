<?php

declare(strict_types=1);

namespace Liopay\VietQR\Helper;

use Liopay\VietQR\Exception\InvalidFormatException;

/**
 * TLV (Tag-Length-Value) encoding/decoding helper for VietQR
 *
 * Format: ID (2 digits) + Length (2 digits) + Value (variable)
 * - ID: Two-digit numeric value (00-99)
 * - Length: Two-digit numeric value (01-99) representing byte count
 * - Value: Variable length data (1-99 bytes)
 *
 * @package Liopay\VietQR\Helper
 */
final class TLVHelper
{
    /**
     * Encode data into TLV format
     *
     * @param string $id Two-digit ID (00-99)
     * @param string $value Value to encode
     * @return string TLV encoded string
     * @throws \InvalidArgumentException If ID or value is invalid
     */
    public function encode(string $id, string $value): string
    {
        $this->validateId($id);
        $this->validateValue($value);

        $length = $this->getLength($value);

        return $id . $length . $value;
    }

    /**
     * Decode TLV string into array of [id => value]
     *
     * @param string $tlvString TLV encoded string
     * @return array<string, string> Associative array of ID => value pairs
     * @throws InvalidFormatException If TLV format is invalid
     */
    public function decode(string $tlvString): array
    {
        $result = [];
        $offset = 0;
        $length = strlen($tlvString);

        while ($offset < $length) {
            $tlv = $this->decodeSingle($tlvString, $offset);
            $result[$tlv['id']] = $tlv['value'];
            $offset = $tlv['next'];
        }

        return $result;
    }

    /**
     * Decode single TLV object at position
     *
     * @param string $tlvString Source string
     * @param int $offset Starting position
     * @return array{id: string, length: int, value: string, next: int}
     * @throws InvalidFormatException If TLV format is invalid at position
     */
    public function decodeSingle(string $tlvString, int $offset = 0): array
    {
        $totalLength = strlen($tlvString);

        // Need at least 4 characters (2 for ID, 2 for length)
        if ($offset + 4 > $totalLength) {
            throw new InvalidFormatException(
                "Invalid TLV format at offset {$offset}: insufficient data",
            );
        }

        // Extract ID (2 digits)
        $id = substr($tlvString, $offset, 2);
        if (!ctype_digit($id)) {
            throw new InvalidFormatException(
                "Invalid TLV ID at offset {$offset}: '{$id}' must be numeric",
            );
        }

        // Extract Length (2 digits)
        $lengthStr = substr($tlvString, $offset + 2, 2);
        if (!ctype_digit($lengthStr)) {
            throw new InvalidFormatException(
                "Invalid TLV length at offset " . ($offset + 2) . ": '{$lengthStr}' must be numeric",
            );
        }

        $length = (int) $lengthStr;

        // Check if we have enough data for the value
        $valueStart = $offset + 4;
        if ($valueStart + $length > $totalLength) {
            throw new InvalidFormatException(
                "Invalid TLV value at offset {$valueStart}: expected {$length} bytes but only "
                    . ($totalLength - $valueStart) . " remaining",
            );
        }

        // Extract value
        $value = substr($tlvString, $valueStart, $length);

        return [
            'id' => $id,
            'length' => $length,
            'value' => $value,
            'next' => $valueStart + $length,
        ];
    }

    /**
     * Validate TLV format without full parsing
     *
     * @param string $tlvString TLV string to validate
     * @return bool True if valid
     */
    public function validate(string $tlvString): bool
    {
        try {
            $this->decode($tlvString);
            return true;
        } catch (InvalidFormatException $e) {
            return false;
        }
    }

    /**
     * Get length of value as 2-digit string
     *
     * @param string $value Value to measure
     * @return string Two-digit length
     */
    private function getLength(string $value): string
    {
        $length = strlen($value);
        return str_pad((string) $length, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Validate ID format
     *
     * @param string $id ID to validate
     * @throws \InvalidArgumentException If ID is invalid
     */
    private function validateId(string $id): void
    {
        if (strlen($id) !== 2 || !ctype_digit($id)) {
            throw new \InvalidArgumentException(
                "Invalid TLV ID '{$id}': must be exactly 2 numeric digits",
            );
        }
    }

    /**
     * Validate value format
     *
     * @param string $value Value to validate
     * @throws \InvalidArgumentException If value is invalid
     */
    private function validateValue(string $value): void
    {
        $length = strlen($value);

        if ($length < 1) {
            throw new \InvalidArgumentException('TLV value cannot be empty');
        }

        if ($length > 99) {
            throw new \InvalidArgumentException(
                "TLV value length {$length} exceeds maximum of 99 bytes",
            );
        }
    }
}
