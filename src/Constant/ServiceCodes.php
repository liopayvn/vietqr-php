<?php

declare(strict_types=1);

namespace Liopay\VietQR\Constant;

/**
 * Service code constants for VietQR
 *
 * @package Liopay\VietQR\Constant
 */
final class ServiceCodes
{
    /**
     * Payment service by QR (can be omitted, treated as default)
     */
    public const QRPUSH = 'QRPUSH';

    /**
     * Cash withdrawal service at ATM by QR
     */
    public const QRCASH = 'QRCASH';

    /**
     * Inter-Bank Fund Transfer 24/7 to Card service by QR
     */
    public const QRIBFTTC = 'QRIBFTTC';

    /**
     * Inter-Bank Fund Transfer 24/7 to Account service by QR
     */
    public const QRIBFTTA = 'QRIBFTTA';

    /**
     * Get all valid service codes
     *
     * @return array<string>
     */
    public static function getAll(): array
    {
        return [
            self::QRPUSH,
            self::QRCASH,
            self::QRIBFTTC,
            self::QRIBFTTA,
        ];
    }

    /**
     * Check if service code is valid
     *
     * @param string $code Service code to check
     * @return bool
     */
    public static function isValid(string $code): bool
    {
        return in_array($code, self::getAll(), true);
    }
}
