<?php

declare(strict_types=1);

namespace Liopay\VietQR\Builder;

use Liopay\VietQR\Constant\{DataObjectId, ServiceCodes, Specifications};
use Liopay\VietQR\Exception\{MissingRequiredFieldException, ValidationException};

/**
 * QR IBFT (Inter-Bank Fund Transfer) Builder
 *
 * For peer-to-peer transfer scenarios (to card or account)
 *
 * @package Liopay\VietQR\Builder
 */
final class QRIBFTBuilder extends QRBuilder
{
    /**
     * Set beneficiary bank BIN (instead of acquirer)
     *
     * @return static
     */
    public function setBeneficiaryBankBin(string $bin): self
    {
        return $this->setAcquirerBankBin($bin);
    }

    /**
     * Set consumer/beneficiary account or card
     *
     * @return static
     */
    public function setConsumerId(string $consumerId): self
    {
        return $this->setMerchantId($consumerId);
    }

    /**
     * Set service code for IBFT to card
     *
     * @return static
     */
    public function setIBFTToCard(): self
    {
        return $this->setServiceCode(ServiceCodes::QRIBFTTC);
    }

    /**
     * Set service code for IBFT to account
     *
     * @return static
     */
    public function setIBFTToAccount(): self
    {
        return $this->setServiceCode(ServiceCodes::QRIBFTTA);
    }

    /**
     * Build complete QR IBFT string
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

        // ID 01: Point of Initiation Method (mandatory)
        if (!isset($this->data['pointOfInitiation'])) {
            throw new MissingRequiredFieldException('Point of Initiation is required for QR IBFT');
        }
        $qr .= $this->tlvHelper->encode(
            DataObjectId::POINT_OF_INITIATION_METHOD,
            $this->data['pointOfInitiation'],
        );

        // ID 38: Consumer Account Information (mandatory)
        $qr .= $this->buildMerchantAccountInfo();

        // ID 52: Merchant Category Code (optional for IBFT)
        if (isset($this->data['mcc'])) {
            $qr .= $this->tlvHelper->encode(
                DataObjectId::MERCHANT_CATEGORY_CODE,
                $this->data['mcc'],
            );
        }

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

        // ID 59: Merchant Name (optional for IBFT)
        if (isset($this->data['merchantName'])) {
            $qr .= $this->tlvHelper->encode(
                DataObjectId::MERCHANT_NAME,
                $this->data['merchantName'],
            );
        }

        // ID 60: Merchant City (optional for IBFT)
        if (isset($this->data['merchantCity'])) {
            $qr .= $this->tlvHelper->encode(
                DataObjectId::MERCHANT_CITY,
                $this->data['merchantCity'],
            );
        }

        // ID 61: Postal Code (optional)
        if (isset($this->data['postalCode'])) {
            $qr .= $this->tlvHelper->encode(
                DataObjectId::POSTAL_CODE,
                $this->data['postalCode'],
            );
        }

        // ID 62: Additional Data Field Template (conditional)
        $additionalData = $this->buildAdditionalDataField();
        if ($additionalData !== '') {
            $qr .= $additionalData;
        }

        // ID 63: CRC (mandatory) - added by CRCHelper
        return $this->crcHelper->append($qr);
    }

    /**
     * Validate required fields for QR IBFT
     *
     * @throws MissingRequiredFieldException
     */
    protected function validateRequiredFields(): void
    {
        $required = [
            'pointOfInitiation' => 'Point of Initiation',
            'bankBin' => 'Beneficiary Bank BIN',
            'merchantId' => 'Consumer ID',
            'serviceCode' => 'Service Code (QRIBFTTC or QRIBFTTA)',
            'currency' => 'Currency',
            'country' => 'Country',
        ];

        foreach ($required as $field => $label) {
            if (!isset($this->data[$field]) || $this->data[$field] === '') {
                throw new MissingRequiredFieldException("Required field missing: {$label}");
            }
        }

        // Validate service code is IBFT type
        if (!in_array($this->data['serviceCode'], [ServiceCodes::QRIBFTTC, ServiceCodes::QRIBFTTA], true)) {
            throw new ValidationException(
                "Service code must be QRIBFTTC or QRIBFTTA for IBFT, got: {$this->data['serviceCode']}",
            );
        }
    }
}
