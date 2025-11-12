<?php

declare(strict_types=1);

namespace Liopay\VietQR\Constant;

/**
 * VietQR specification constants and limits
 *
 * Based on NAPAS QR Switching Technical Specifications v1.5.2
 *
 * @package Liopay\VietQR\Constant
 */
final class Specifications
{
    // Required constant values
    public const PAYLOAD_FORMAT = '01';
    public const GUID_AID = 'A000000727';
    public const CURRENCY_VND = '704';
    public const COUNTRY_VN = 'VN';

    // Field length constraints
    public const BANK_BIN_LENGTH = 6;
    public const MCC_LENGTH = 4;
    public const CRC_LENGTH = 4;
    public const PAYLOAD_FORMAT_LENGTH = 2;
    public const POINT_OF_INITIATION_LENGTH = 2;
    public const CURRENCY_LENGTH = 3;
    public const COUNTRY_LENGTH = 2;

    // Maximum field lengths
    public const MAX_MERCHANT_ID = 19;
    public const MAX_MERCHANT_NAME = 25;
    public const MAX_MERCHANT_CITY = 15;
    public const MAX_POSTAL_CODE = 10;
    public const MAX_AMOUNT = 13;
    public const MAX_ADDITIONAL_FIELD = 25;
    public const MAX_TLV_VALUE = 99;
    public const MAX_SERVICE_CODE = 10;

    // Minimum field lengths
    public const MIN_TLV_VALUE = 1;
}
