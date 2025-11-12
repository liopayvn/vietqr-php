<?php

declare(strict_types=1);

namespace Liopay\VietQR\Constant;

/**
 * Data Object ID constants for VietQR fields
 *
 * @package Liopay\VietQR\Constant
 */
final class DataObjectId
{
    // Root level data objects
    public const PAYLOAD_FORMAT_INDICATOR = '00';
    public const POINT_OF_INITIATION_METHOD = '01';
    public const MERCHANT_ACCOUNT_INFORMATION = '38';
    public const MERCHANT_CATEGORY_CODE = '52';
    public const TRANSACTION_CURRENCY = '53';
    public const TRANSACTION_AMOUNT = '54';
    public const TIP_OR_CONVENIENCE_INDICATOR = '55';
    public const CONVENIENCE_FEE_FIXED = '56';
    public const CONVENIENCE_FEE_PERCENTAGE = '57';
    public const COUNTRY_CODE = '58';
    public const MERCHANT_NAME = '59';
    public const MERCHANT_CITY = '60';
    public const POSTAL_CODE = '61';
    public const ADDITIONAL_DATA_FIELD_TEMPLATE = '62';
    public const CRC = '63';
    public const MERCHANT_INFORMATION_LANGUAGE_TEMPLATE = '64';

    // Merchant Account Information sub-objects (within ID 38)
    public const MAI_GUID = '00';
    public const MAI_PAYMENT_NETWORK = '01';
    public const MAI_SERVICE_CODE = '02';

    // Payment Network sub-objects (within ID 38.01)
    public const PN_BANK_BIN = '00';
    public const PN_MERCHANT_ID = '01';

    // Additional Data Field sub-objects (within ID 62)
    public const ADF_BILL_NUMBER = '01';
    public const ADF_MOBILE_NUMBER = '02';
    public const ADF_STORE_LABEL = '03';
    public const ADF_LOYALTY_NUMBER = '04';
    public const ADF_REFERENCE_LABEL = '05';
    public const ADF_CUSTOMER_LABEL = '06';
    public const ADF_TERMINAL_LABEL = '07';
    public const ADF_PURPOSE_OF_TRANSACTION = '08';
    public const ADF_ADDITIONAL_CONSUMER_DATA_REQUEST = '09';
}
