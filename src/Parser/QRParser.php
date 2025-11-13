<?php

declare(strict_types=1);

namespace Liopay\VietQR\Parser;

use Liopay\VietQR\Constant\{DataObjectId, ServiceCodes, Specifications};
use Liopay\VietQR\DTO\{AdditionalDataField, ParsedQRData};
use Liopay\VietQR\Exception\{InvalidCRCException, InvalidFormatException, InvalidLengthException, ValidationException};
use Liopay\VietQR\Helper\{TLVHelper, CRCHelper};

/**
 * QR Parser
 *
 * Parses QR code strings and extracts data
 *
 * @package Liopay\VietQR\Parser
 */
final class QRParser
{
    private TLVHelper $tlvHelper;
    private CRCHelper $crcHelper;

    public function __construct(?TLVHelper $tlvHelper = null, ?CRCHelper $crcHelper = null)
    {
        $this->tlvHelper = $tlvHelper ?? new TLVHelper();
        $this->crcHelper = $crcHelper ?? new CRCHelper();
    }

    /**
     * Parse a QR code string
     *
     * @param string $qrString The QR code string to parse
     * @param bool $verifyCRC Whether to verify CRC (default: true)
     * @return ParsedQRData The parsed QR data
     * @throws InvalidFormatException
     * @throws InvalidCRCException
     */
    public function parse(string $qrString, bool $verifyCRC = true): ParsedQRData
    {
        if ($verifyCRC && !$this->crcHelper->verify($qrString)) {
            throw new InvalidCRCException('QR code has invalid CRC checksum');
        }

        $data = new ParsedQRData();
        $parsed = $this->tlvHelper->decode($qrString);

        // Parse root level fields
        $this->parseRootFields($parsed, $data);

        // Parse merchant account information (ID 38)
        /** @phpstan-ignore-next-line */
        if (isset($parsed[DataObjectId::MERCHANT_ACCOUNT_INFORMATION])) {
            $this->parseMerchantAccountInfo($parsed[DataObjectId::MERCHANT_ACCOUNT_INFORMATION], $data);
        }

        // Parse additional data field (ID 62)
        /** @phpstan-ignore-next-line */
        if (isset($parsed[DataObjectId::ADDITIONAL_DATA_FIELD_TEMPLATE])) {
            $this->parseAdditionalDataField($parsed[DataObjectId::ADDITIONAL_DATA_FIELD_TEMPLATE], $data);
        }

        // Determine QR type based on service code
        $this->determineQRType($data);

        return $data;
    }

    /**
     * Parse root level fields
     *
     * @param array<string, string> $parsed
     */
    private function parseRootFields(array $parsed, ParsedQRData $data): void
    {
        // ID 00: Payload Format Indicator
        if (isset($parsed[DataObjectId::PAYLOAD_FORMAT_INDICATOR])) {
            $data->setPayloadFormat($parsed[DataObjectId::PAYLOAD_FORMAT_INDICATOR]);
        }

        // ID 01: Point of Initiation Method
        if (isset($parsed[DataObjectId::POINT_OF_INITIATION_METHOD])) {
            $data->setPointOfInitiation($parsed[DataObjectId::POINT_OF_INITIATION_METHOD]);
        }

        // ID 52: Merchant Category Code
        /** @phpstan-ignore-next-line */
        if (isset($parsed[DataObjectId::MERCHANT_CATEGORY_CODE])) {
            $data->setMerchantCategoryCode($parsed[DataObjectId::MERCHANT_CATEGORY_CODE]);
        }

        // ID 53: Transaction Currency
        /** @phpstan-ignore-next-line */
        if (isset($parsed[DataObjectId::TRANSACTION_CURRENCY])) {
            $currency = $parsed[DataObjectId::TRANSACTION_CURRENCY];
            $this->validateCurrency($currency);
            $data->setCurrency($currency);
        }

        // ID 54: Transaction Amount
        /** @phpstan-ignore-next-line */
        if (isset($parsed[DataObjectId::TRANSACTION_AMOUNT])) {
            $data->setAmount($parsed[DataObjectId::TRANSACTION_AMOUNT]);
        }

        // ID 58: Country Code
        /** @phpstan-ignore-next-line */
        if (isset($parsed[DataObjectId::COUNTRY_CODE])) {
            $country = $parsed[DataObjectId::COUNTRY_CODE];
            $this->validateCountry($country);
            $data->setCountry($country);
        }

        // ID 59: Merchant Name
        /** @phpstan-ignore-next-line */
        if (isset($parsed[DataObjectId::MERCHANT_NAME])) {
            $data->setMerchantName($parsed[DataObjectId::MERCHANT_NAME]);
        }

        // ID 60: Merchant City
        /** @phpstan-ignore-next-line */
        if (isset($parsed[DataObjectId::MERCHANT_CITY])) {
            $data->setMerchantCity($parsed[DataObjectId::MERCHANT_CITY]);
        }

        // ID 61: Postal Code
        /** @phpstan-ignore-next-line */
        if (isset($parsed[DataObjectId::POSTAL_CODE])) {
            $data->setPostalCode($parsed[DataObjectId::POSTAL_CODE]);
        }

        // ID 63: CRC
        /** @phpstan-ignore-next-line */
        if (isset($parsed[DataObjectId::CRC])) {
            $data->setCRC($parsed[DataObjectId::CRC]);
        }
    }

    /**
     * Parse merchant account information (ID 38)
     */
    private function parseMerchantAccountInfo(string $value, ParsedQRData $data): void
    {
        $mai = $this->tlvHelper->decode($value);

        // ID 00: GUID
        if (isset($mai[DataObjectId::MAI_GUID])) {
            $data->setGUID($mai[DataObjectId::MAI_GUID]);
        }

        // ID 01: Payment Network
        if (isset($mai[DataObjectId::MAI_PAYMENT_NETWORK])) {
            $paymentNetwork = $this->tlvHelper->decode($mai[DataObjectId::MAI_PAYMENT_NETWORK]);

            // ID 00: Bank BIN
            if (isset($paymentNetwork[DataObjectId::PN_BANK_BIN])) {
                $data->setBankBin($paymentNetwork[DataObjectId::PN_BANK_BIN]);
            }

            // ID 01: Merchant ID
            if (isset($paymentNetwork[DataObjectId::PN_MERCHANT_ID])) {
                $data->setMerchantId($paymentNetwork[DataObjectId::PN_MERCHANT_ID]);
            }
        }

        // ID 02: Service Code
        if (isset($mai[DataObjectId::MAI_SERVICE_CODE])) {
            $serviceCode = $mai[DataObjectId::MAI_SERVICE_CODE];
            $this->validateServiceCode($serviceCode);
            $data->setServiceCode($serviceCode);
        }
    }

    /**
     * Parse additional data field (ID 62)
     */
    private function parseAdditionalDataField(string $value, ParsedQRData $data): void
    {
        $adf = $this->tlvHelper->decode($value);
        $additionalData = new AdditionalDataField();

        // ID 01: Bill Number
        if (isset($adf[DataObjectId::ADF_BILL_NUMBER])) {
            $additionalData->setBillNumber($adf[DataObjectId::ADF_BILL_NUMBER]);
        }

        // ID 02: Mobile Number
        if (isset($adf[DataObjectId::ADF_MOBILE_NUMBER])) {
            $additionalData->setMobileNumber($adf[DataObjectId::ADF_MOBILE_NUMBER]);
        }

        // ID 03: Store Label
        if (isset($adf[DataObjectId::ADF_STORE_LABEL])) {
            $additionalData->setStoreLabel($adf[DataObjectId::ADF_STORE_LABEL]);
        }

        // ID 04: Loyalty Number
        if (isset($adf[DataObjectId::ADF_LOYALTY_NUMBER])) {
            $additionalData->setLoyaltyNumber($adf[DataObjectId::ADF_LOYALTY_NUMBER]);
        }

        // ID 05: Reference Label
        if (isset($adf[DataObjectId::ADF_REFERENCE_LABEL])) {
            $additionalData->setReferenceLabel($adf[DataObjectId::ADF_REFERENCE_LABEL]);
        }

        // ID 06: Customer Label
        if (isset($adf[DataObjectId::ADF_CUSTOMER_LABEL])) {
            $additionalData->setCustomerLabel($adf[DataObjectId::ADF_CUSTOMER_LABEL]);
        }

        // ID 07: Terminal Label
        if (isset($adf[DataObjectId::ADF_TERMINAL_LABEL])) {
            $additionalData->setTerminalLabel($adf[DataObjectId::ADF_TERMINAL_LABEL]);
        }

        // ID 08: Purpose of Transaction
        if (isset($adf[DataObjectId::ADF_PURPOSE_OF_TRANSACTION])) {
            $additionalData->setPurposeOfTransaction($adf[DataObjectId::ADF_PURPOSE_OF_TRANSACTION]);
        }

        // ID 09: Additional Consumer Data Request
        if (isset($adf[DataObjectId::ADF_ADDITIONAL_CONSUMER_DATA_REQUEST])) {
            $additionalData->setAdditionalConsumerDataRequest($adf[DataObjectId::ADF_ADDITIONAL_CONSUMER_DATA_REQUEST]);
        }

        $data->setAdditionalData($additionalData);
    }

    /**
     * Determine QR type based on service code and other indicators
     */
    private function determineQRType(ParsedQRData $data): void
    {
        $serviceCode = $data->getServiceCode();

        if ($serviceCode === ServiceCodes::QRCASH) {
            $data->setQRType('QRCASH');
        } elseif ($serviceCode === ServiceCodes::QRIBFTTC) {
            $data->setQRType('QRIBFTTC');
        } elseif ($serviceCode === ServiceCodes::QRIBFTTA) {
            $data->setQRType('QRIBFTTA');
        } elseif ($serviceCode === ServiceCodes::QRPUSH || $serviceCode === null) {
            // QRPUSH can have explicit service code or no service code
            $data->setQRType('QRPUSH');
        } else {
            $data->setQRType('UNKNOWN');
        }
    }

    /**
     * Validate currency code from parsed QR data
     *
     * @throws InvalidFormatException If currency is invalid
     */
    private function validateCurrency(string $currency): void
    {
        if (strlen($currency) !== Specifications::CURRENCY_LENGTH) {
            throw new InvalidFormatException(
                "Currency must be exactly " . Specifications::CURRENCY_LENGTH . " characters, got " . strlen($currency)
            );
        }

        if (!ctype_digit($currency)) {
            throw new InvalidFormatException("Currency must be numeric, got: {$currency}");
        }
    }

    /**
     * Validate country code from parsed QR data
     *
     * @throws InvalidFormatException If country code is invalid
     */
    private function validateCountry(string $country): void
    {
        if (strlen($country) !== Specifications::COUNTRY_LENGTH) {
            throw new InvalidFormatException(
                "Country must be exactly " . Specifications::COUNTRY_LENGTH . " characters, got " . strlen($country)
            );
        }

        if (!ctype_alpha($country)) {
            throw new InvalidFormatException("Country must contain only alphabetic characters, got: {$country}");
        }

        if ($country !== strtoupper($country)) {
            throw new InvalidFormatException("Country must be uppercase, got: {$country}");
        }
    }

    /**
     * Validate service code from parsed QR data
     *
     * @throws InvalidFormatException If service code is invalid
     */
    private function validateServiceCode(string $serviceCode): void
    {
        if (strlen($serviceCode) > Specifications::MAX_SERVICE_CODE) {
            throw new InvalidFormatException(
                "Service code exceeds maximum length of " . Specifications::MAX_SERVICE_CODE . " characters"
            );
        }

        if (!ServiceCodes::isValid($serviceCode)) {
            throw new InvalidFormatException(
                "Invalid service code: {$serviceCode}. Allowed values: " . implode(', ', ServiceCodes::getAll())
            );
        }
    }
}
