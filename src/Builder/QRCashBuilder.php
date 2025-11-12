<?php

declare(strict_types=1);

namespace Liopay\VietQR\Builder;

use Liopay\VietQR\Constant\{DataObjectId, ServiceCodes, Specifications};
use Liopay\VietQR\Exception\MissingRequiredFieldException;

/**
 * QR Cash Withdrawal Builder
 *
 * For ATM cash withdrawal scenarios
 *
 * @package Liopay\VietQR\Builder
 */
final class QRCashBuilder extends QRBuilder
{
    /**
     * Set ATM ID (uses merchantId internally)
     */
    public function setATMId(string $atmId): self
    {
        return $this->setMerchantId($atmId);
    }

    /**
     * Set service code to QRCASH
     */
    public function setCashService(): self
    {
        return $this->setServiceCode(ServiceCodes::QRCASH);
    }

    /**
     * Build complete QR Cash string
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

        // ID 01: Point of Initiation Method (mandatory for QR Cash)
        if (!isset($this->data['pointOfInitiation'])) {
            throw new MissingRequiredFieldException('Point of Initiation is required for QR Cash');
        }
        $qr .= $this->tlvHelper->encode(
            DataObjectId::POINT_OF_INITIATION_METHOD,
            $this->data['pointOfInitiation'],
        );

        // ID 38: Merchant Account Information (mandatory)
        $qr .= $this->buildMerchantAccountInfo();

        // ID 52: Merchant Category Code (mandatory for QR Cash)
        if (!isset($this->data['mcc'])) {
            throw new MissingRequiredFieldException('Merchant Category Code is required for QR Cash');
        }
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

        // ID 59: Merchant Name (mandatory for QR Cash)
        if (!isset($this->data['merchantName'])) {
            throw new MissingRequiredFieldException('Merchant Name is required for QR Cash');
        }
        $qr .= $this->tlvHelper->encode(
            DataObjectId::MERCHANT_NAME,
            $this->data['merchantName'],
        );

        // ID 60: Merchant City (mandatory for QR Cash)
        if (!isset($this->data['merchantCity'])) {
            throw new MissingRequiredFieldException('Merchant City is required for QR Cash');
        }
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

        // ID 62: Additional Data Field Template (mandatory for QR Cash)
        $additionalData = $this->buildAdditionalDataField();
        if ($additionalData === '') {
            throw new MissingRequiredFieldException('Additional Data Field Template is required for QR Cash');
        }
        $qr .= $additionalData;

        // ID 63: CRC (mandatory) - added by CRCHelper
        return $this->crcHelper->append($qr);
    }

    /**
     * Validate required fields for QR Cash
     *
     * @throws MissingRequiredFieldException
     */
    protected function validateRequiredFields(): void
    {
        $required = [
            'pointOfInitiation' => 'Point of Initiation',
            'bankBin' => 'Acquirer Bank BIN',
            'merchantId' => 'ATM ID',
            'serviceCode' => 'Service Code (QRCASH)',
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

        // Validate service code is QRCASH
        if ($this->data['serviceCode'] !== ServiceCodes::QRCASH) {
            throw new MissingRequiredFieldException(
                "Service code must be QRCASH for Cash Withdrawal, got: {$this->data['serviceCode']}",
            );
        }

        // Validate Additional Data Field requirements for QR Cash
        // Reference Label (ID 05) and Terminal Label (ID 07) are mandatory
        if (!$this->additionalData->getReferenceLabel()) {
            throw new MissingRequiredFieldException('Reference Label is required in Additional Data Field for QR Cash');
        }

        if (!$this->additionalData->getTerminalLabel()) {
            throw new MissingRequiredFieldException('Terminal Label is required in Additional Data Field for QR Cash');
        }
    }
}
