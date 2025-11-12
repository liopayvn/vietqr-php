<?php

declare(strict_types=1);

namespace Liopay\VietQR\Builder;

use Liopay\VietQR\Constant\{DataObjectId, ServiceCodes, Specifications};
use Liopay\VietQR\Exception\MissingRequiredFieldException;

/**
 * QR PUSH Payment Builder
 *
 * For merchant payment scenarios
 *
 * @package Liopay\VietQR\Builder
 */
final class QRPushBuilder extends QRBuilder
{
    /**
     * Build complete QR PUSH string
     *
     * @return string Complete QR code string with CRC
     * @throws MissingRequiredFieldException
     */
    public function build(): string
    {
        $this->validateRequiredFields();

        $qr = '';

        // ID 00: Payload Format Indicator (mandatory)
        $qr .= $this->tlvHelper->encode(
            DataObjectId::PAYLOAD_FORMAT_INDICATOR,
            $this->data['payloadFormat'],
        );

        // ID 01: Point of Initiation Method (optional)
        if (isset($this->data['pointOfInitiation'])) {
            $qr .= $this->tlvHelper->encode(
                DataObjectId::POINT_OF_INITIATION_METHOD,
                $this->data['pointOfInitiation'],
            );
        }

        // ID 38: Merchant Account Information (mandatory)
        $qr .= $this->buildMerchantAccountInfo();

        // ID 52: Merchant Category Code (mandatory)
        $qr .= $this->tlvHelper->encode(
            DataObjectId::MERCHANT_CATEGORY_CODE,
            $this->data['mcc'],
        );

        // ID 53: Transaction Currency (mandatory)
        $qr .= $this->tlvHelper->encode(
            DataObjectId::TRANSACTION_CURRENCY,
            $this->data['currency'],
        );

        // ID 54: Transaction Amount (conditional)
        if (isset($this->data['amount'])) {
            $qr .= $this->tlvHelper->encode(
                DataObjectId::TRANSACTION_AMOUNT,
                $this->data['amount'],
            );
        }

        // ID 58: Country Code (mandatory)
        $qr .= $this->tlvHelper->encode(
            DataObjectId::COUNTRY_CODE,
            $this->data['country'],
        );

        // ID 59: Merchant Name (mandatory)
        $qr .= $this->tlvHelper->encode(
            DataObjectId::MERCHANT_NAME,
            $this->data['merchantName'],
        );

        // ID 60: Merchant City (mandatory)
        $qr .= $this->tlvHelper->encode(
            DataObjectId::MERCHANT_CITY,
            $this->data['merchantCity'],
        );

        // ID 61: Postal Code (optional)
        if (isset($this->data['postalCode'])) {
            $qr .= $this->tlvHelper->encode(
                DataObjectId::POSTAL_CODE,
                $this->data['postalCode'],
            );
        }

        // ID 62: Additional Data Field Template (optional)
        $additionalData = $this->buildAdditionalDataField();
        if ($additionalData !== '') {
            $qr .= $additionalData;
        }

        // ID 63: CRC (mandatory) - added by CRCHelper
        return $this->crcHelper->append($qr);
    }

    /**
     * Validate required fields for QR PUSH
     *
     * @throws MissingRequiredFieldException
     */
    protected function validateRequiredFields(): void
    {
        $required = [
            'bankBin' => 'Acquirer Bank BIN',
            'merchantId' => 'Merchant ID',
            'mcc' => 'Merchant Category Code',
            'currency' => 'Currency',
            'country' => 'Country',
            'merchantName' => 'Merchant Name',
            'merchantCity' => 'Merchant City',
        ];

        foreach ($required as $field => $label) {
            if (!isset($this->data[$field]) || $this->data[$field] === '') {
                throw new MissingRequiredFieldException("Required field missing: {$label}");
            }
        }

        // Dynamic QR requires amount
        if (isset($this->data['pointOfInitiation']) && $this->data['pointOfInitiation'] === '12') {
            if (!isset($this->data['amount'])) {
                throw new MissingRequiredFieldException(
                    'Transaction amount is required for dynamic QR codes',
                );
            }
        }
    }
}
