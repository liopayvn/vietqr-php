<?php

declare(strict_types=1);

namespace Liopay\VietQR\Builder;

use Liopay\VietQR\Constant\{DataObjectId, Specifications};
use Liopay\VietQR\DTO\AdditionalDataField;
use Liopay\VietQR\Exception\{InvalidLengthException, MissingRequiredFieldException, ValidationException};
use Liopay\VietQR\Helper\{TLVHelper, CRCHelper};

/**
 * Base QR Builder - Abstract class for all QR types
 *
 * @package Liopay\VietQR\Builder
 */
abstract class QRBuilder
{
    protected TLVHelper $tlvHelper;
    protected CRCHelper $crcHelper;

    /** @var array<string, mixed> */
    protected array $data = [];

    protected AdditionalDataField $additionalData;

    public function __construct(TLVHelper $tlvHelper, CRCHelper $crcHelper)
    {
        $this->tlvHelper = $tlvHelper;
        $this->crcHelper = $crcHelper;
        $this->additionalData = new AdditionalDataField();
        $this->reset();
    }

    /**
     * Set payload format indicator (always "01")
     *
     * @return static
     */
    public function setPayloadFormat(string $format = Specifications::PAYLOAD_FORMAT): self
    {
        $this->data['payloadFormat'] = $format;
        return $this;
    }

    /**
     * Set point of initiation method
     *
     * @param string $method "11" for static, "12" for dynamic
     * @return static
     */
    public function setPointOfInitiation(string $method): self
    {
        if (!in_array($method, ['11', '12'], true)) {
            throw new ValidationException("Invalid point of initiation: {$method}. Must be '11' or '12'");
        }
        $this->data['pointOfInitiation'] = $method;
        return $this;
    }

    /**
     * Set GUID (Application Identifier)
     *
     * @return static
     */
    public function setGUID(string $guid = Specifications::GUID_AID): self
    {
        $this->data['guid'] = $guid;
        return $this;
    }

    /**
     * Set acquirer bank BIN
     *
     * @param string $bin 6-digit bank BIN
     * @return static
     */
    public function setAcquirerBankBin(string $bin): self
    {
        $this->validateNumericField('Bank BIN', $bin, Specifications::BANK_BIN_LENGTH);
        $this->data['bankBin'] = $bin;
        return $this;
    }

    /**
     * Set merchant ID
     *
     * @param string $merchantId Max 19 characters
     * @return static
     */
    public function setMerchantId(string $merchantId): self
    {
        $this->validateFieldLength('Merchant ID', $merchantId, Specifications::MAX_MERCHANT_ID);
        $this->data['merchantId'] = $merchantId;
        return $this;
    }

    /**
     * Set service code
     *
     * @return static
     */
    public function setServiceCode(?string $serviceCode): self
    {
        if ($serviceCode !== null) {
            $this->validateFieldLength('Service Code', $serviceCode, Specifications::MAX_SERVICE_CODE);
        }
        $this->data['serviceCode'] = $serviceCode;
        return $this;
    }

    /**
     * Set merchant category code
     *
     * @param string $mcc 4-digit MCC
     * @return static
     */
    public function setMerchantCategoryCode(string $mcc): self
    {
        $this->validateNumericField('MCC', $mcc, Specifications::MCC_LENGTH);
        $this->data['mcc'] = $mcc;
        return $this;
    }

    /**
     * Set transaction currency (default VND = "704")
     *
     * @return static
     */
    public function setCurrency(string $currency = Specifications::CURRENCY_VND): self
    {
        $this->data['currency'] = $currency;
        return $this;
    }

    /**
     * Set transaction amount
     *
     * @param string|null $amount Numeric amount with optional decimal point
     * @return static
     */
    public function setAmount(?string $amount): self
    {
        if ($amount !== null) {
            $this->validateAmount($amount);
        }
        $this->data['amount'] = $amount;
        return $this;
    }

    /**
     * Set country code (default VN)
     *
     * @return static
     */
    public function setCountry(string $country = Specifications::COUNTRY_VN): self
    {
        $this->data['country'] = $country;
        return $this;
    }

    /**
     * Set merchant name
     *
     * @param string|null $name Max 25 characters
     * @return static
     */
    public function setMerchantName(?string $name): self
    {
        if ($name !== null) {
            $this->validateFieldLength('Merchant Name', $name, Specifications::MAX_MERCHANT_NAME);
        }
        $this->data['merchantName'] = $name;
        return $this;
    }

    /**
     * Set merchant city
     *
     * @param string|null $city Max 15 characters
     * @return static
     */
    public function setMerchantCity(?string $city): self
    {
        if ($city !== null) {
            $this->validateFieldLength('Merchant City', $city, Specifications::MAX_MERCHANT_CITY);
        }
        $this->data['merchantCity'] = $city;
        return $this;
    }

    /**
     * Set postal code
     *
     * @param string|null $postalCode Max 10 characters
     * @return static
     */
    public function setPostalCode(?string $postalCode): self
    {
        if ($postalCode !== null) {
            $this->validateFieldLength('Postal Code', $postalCode, Specifications::MAX_POSTAL_CODE);
        }
        $this->data['postalCode'] = $postalCode;
        return $this;
    }

    // Additional Data Field setters

    /**
     * @return static
     */
    public function setBillNumber(?string $billNumber): self
    {
        $this->additionalData->setBillNumber($billNumber);
        return $this;
    }

    /**
     * @return static
     */
    public function setMobileNumber(?string $mobile): self
    {
        $this->additionalData->setMobileNumber($mobile);
        return $this;
    }

    /**
     * @return static
     */
    public function setStoreLabel(?string $store): self
    {
        $this->additionalData->setStoreLabel($store);
        return $this;
    }

    /**
     * @return static
     */
    public function setLoyaltyNumber(?string $loyalty): self
    {
        $this->additionalData->setLoyaltyNumber($loyalty);
        return $this;
    }

    /**
     * @return static
     */
    public function setReferenceLabel(?string $reference): self
    {
        $this->additionalData->setReferenceLabel($reference);
        return $this;
    }

    /**
     * @return static
     */
    public function setCustomerLabel(?string $customer): self
    {
        $this->additionalData->setCustomerLabel($customer);
        return $this;
    }

    /**
     * @return static
     */
    public function setTerminalLabel(?string $terminal): self
    {
        $this->additionalData->setTerminalLabel($terminal);
        return $this;
    }

    /**
     * @return static
     */
    public function setPurposeOfTransaction(?string $purpose): self
    {
        $this->additionalData->setPurposeOfTransaction($purpose);
        return $this;
    }

    /**
     * Build merchant account information (ID 38)
     */
    protected function buildMerchantAccountInfo(): string
    {
        $guid = $this->data['guid'] ?? Specifications::GUID_AID;
        $bankBin = $this->data['bankBin'];
        $merchantId = $this->data['merchantId'];

        // Build payment network sub-object (ID 01 within 38)
        $paymentNetwork = $this->tlvHelper->encode(DataObjectId::PN_BANK_BIN, $bankBin);
        $paymentNetwork .= $this->tlvHelper->encode(DataObjectId::PN_MERCHANT_ID, $merchantId);

        // Build merchant account info
        $mai = $this->tlvHelper->encode(DataObjectId::MAI_GUID, $guid);
        $mai .= $this->tlvHelper->encode(DataObjectId::MAI_PAYMENT_NETWORK, $paymentNetwork);

        // Add service code if set
        if (isset($this->data['serviceCode'])) {
            $mai .= $this->tlvHelper->encode(DataObjectId::MAI_SERVICE_CODE, $this->data['serviceCode']);
        }

        return $this->tlvHelper->encode(DataObjectId::MERCHANT_ACCOUNT_INFORMATION, $mai);
    }

    /**
     * Build additional data field (ID 62)
     */
    protected function buildAdditionalDataField(): string
    {
        if (!$this->additionalData->hasData()) {
            return '';
        }

        $adf = '';

        if ($this->additionalData->getBillNumber()) {
            $adf .= $this->tlvHelper->encode(DataObjectId::ADF_BILL_NUMBER, $this->additionalData->getBillNumber());
        }
        if ($this->additionalData->getMobileNumber()) {
            $adf .= $this->tlvHelper->encode(DataObjectId::ADF_MOBILE_NUMBER, $this->additionalData->getMobileNumber());
        }
        if ($this->additionalData->getStoreLabel()) {
            $adf .= $this->tlvHelper->encode(DataObjectId::ADF_STORE_LABEL, $this->additionalData->getStoreLabel());
        }
        if ($this->additionalData->getLoyaltyNumber()) {
            $adf .= $this->tlvHelper->encode(DataObjectId::ADF_LOYALTY_NUMBER, $this->additionalData->getLoyaltyNumber());
        }
        if ($this->additionalData->getReferenceLabel()) {
            $adf .= $this->tlvHelper->encode(DataObjectId::ADF_REFERENCE_LABEL, $this->additionalData->getReferenceLabel());
        }
        if ($this->additionalData->getCustomerLabel()) {
            $adf .= $this->tlvHelper->encode(DataObjectId::ADF_CUSTOMER_LABEL, $this->additionalData->getCustomerLabel());
        }
        if ($this->additionalData->getTerminalLabel()) {
            $adf .= $this->tlvHelper->encode(DataObjectId::ADF_TERMINAL_LABEL, $this->additionalData->getTerminalLabel());
        }
        if ($this->additionalData->getPurposeOfTransaction()) {
            $adf .= $this->tlvHelper->encode(DataObjectId::ADF_PURPOSE_OF_TRANSACTION, $this->additionalData->getPurposeOfTransaction());
        }

        return $adf ? $this->tlvHelper->encode(DataObjectId::ADDITIONAL_DATA_FIELD_TEMPLATE, $adf) : '';
    }

    /**
     * Validate numeric field
     */
    protected function validateNumericField(string $fieldName, string $value, int $expectedLength): void
    {
        if (!ctype_digit($value)) {
            throw new ValidationException("{$fieldName} must be numeric, got: {$value}");
        }

        if (strlen($value) !== $expectedLength) {
            throw new InvalidLengthException(
                "{$fieldName} must be exactly {$expectedLength} digits, got " . strlen($value),
            );
        }
    }

    /**
     * Validate field length
     */
    protected function validateFieldLength(string $fieldName, string $value, int $maxLength): void
    {
        $length = strlen($value);
        if ($length > $maxLength) {
            throw new InvalidLengthException(
                "{$fieldName} exceeds maximum length of {$maxLength} characters (got {$length})",
            );
        }
        if ($length < 1) {
            throw new ValidationException("{$fieldName} cannot be empty");
        }
    }

    /**
     * Validate amount format
     */
    protected function validateAmount(string $amount): void
    {
        if (!preg_match('/^\d+(\.\d*)?$/', $amount)) {
            throw new ValidationException(
                "Invalid amount format: {$amount}. Must be numeric with optional decimal point",
            );
        }

        $this->validateFieldLength('Amount', $amount, Specifications::MAX_AMOUNT);
    }

    /**
     * Validate required fields (implemented by subclasses)
     */
    abstract protected function validateRequiredFields(): void;

    /**
     * Build complete QR string (implemented by subclasses)
     */
    abstract public function build(): string;

    /**
     * Reset builder to initial state
     *
     * @return static
     */
    public function reset(): self
    {
        $this->data = [
            'payloadFormat' => Specifications::PAYLOAD_FORMAT,
            'guid' => Specifications::GUID_AID,
            'currency' => Specifications::CURRENCY_VND,
            'country' => Specifications::COUNTRY_VN,
        ];
        $this->additionalData = new AdditionalDataField();
        return $this;
    }
}
