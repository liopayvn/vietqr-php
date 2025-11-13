<?php

declare(strict_types=1);

namespace Liopay\VietQR\DTO;

use Liopay\VietQR\Constant\Specifications;
use Liopay\VietQR\Exception\{InvalidLengthException, ValidationException};

/**
 * Additional Data Field Template (ID 62)
 *
 * @package Liopay\VietQR\DTO
 */
final class AdditionalDataField
{
    private ?string $billNumber = null;
    private ?string $mobileNumber = null;
    private ?string $storeLabel = null;
    private ?string $loyaltyNumber = null;
    private ?string $referenceLabel = null;
    private ?string $customerLabel = null;
    private ?string $terminalLabel = null;
    private ?string $purposeOfTransaction = null;
    private ?string $additionalConsumerDataRequest = null;

    public function setBillNumber(?string $billNumber): self
    {
        $this->validateField($billNumber, 'Bill Number');
        $this->billNumber = $billNumber;
        return $this;
    }

    public function setMobileNumber(?string $mobileNumber): self
    {
        $this->validateField($mobileNumber, 'Mobile Number');
        $this->mobileNumber = $mobileNumber;
        return $this;
    }

    public function setStoreLabel(?string $storeLabel): self
    {
        $this->validateField($storeLabel, 'Store Label');
        $this->storeLabel = $storeLabel;
        return $this;
    }

    public function setLoyaltyNumber(?string $loyaltyNumber): self
    {
        $this->validateField($loyaltyNumber, 'Loyalty Number');
        $this->loyaltyNumber = $loyaltyNumber;
        return $this;
    }

    public function setReferenceLabel(?string $referenceLabel): self
    {
        $this->validateField($referenceLabel, 'Reference Label');
        $this->referenceLabel = $referenceLabel;
        return $this;
    }

    public function setCustomerLabel(?string $customerLabel): self
    {
        $this->validateField($customerLabel, 'Customer Label');
        $this->customerLabel = $customerLabel;
        return $this;
    }

    public function setTerminalLabel(?string $terminalLabel): self
    {
        $this->validateField($terminalLabel, 'Terminal Label');
        $this->terminalLabel = $terminalLabel;
        return $this;
    }

    public function setPurposeOfTransaction(?string $purposeOfTransaction): self
    {
        $this->validateField($purposeOfTransaction, 'Purpose Of Transaction');
        $this->purposeOfTransaction = $purposeOfTransaction;
        return $this;
    }

    public function setAdditionalConsumerDataRequest(?string $additionalConsumerDataRequest): self
    {
        $this->validateField($additionalConsumerDataRequest, 'Additional Consumer Data Request');
        $this->additionalConsumerDataRequest = $additionalConsumerDataRequest;
        return $this;
    }

    public function getBillNumber(): ?string
    {
        return $this->billNumber;
    }

    public function getMobileNumber(): ?string
    {
        return $this->mobileNumber;
    }

    public function getStoreLabel(): ?string
    {
        return $this->storeLabel;
    }

    public function getLoyaltyNumber(): ?string
    {
        return $this->loyaltyNumber;
    }

    public function getReferenceLabel(): ?string
    {
        return $this->referenceLabel;
    }

    public function getCustomerLabel(): ?string
    {
        return $this->customerLabel;
    }

    public function getTerminalLabel(): ?string
    {
        return $this->terminalLabel;
    }

    public function getPurposeOfTransaction(): ?string
    {
        return $this->purposeOfTransaction;
    }

    public function getAdditionalConsumerDataRequest(): ?string
    {
        return $this->additionalConsumerDataRequest;
    }

    /**
     * Check if any field is set
     *
     * @return bool
     */
    public function hasData(): bool
    {
        return $this->billNumber !== null
            || $this->mobileNumber !== null
            || $this->storeLabel !== null
            || $this->loyaltyNumber !== null
            || $this->referenceLabel !== null
            || $this->customerLabel !== null
            || $this->terminalLabel !== null
            || $this->purposeOfTransaction !== null
            || $this->additionalConsumerDataRequest !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'billNumber' => $this->billNumber,
            'mobileNumber' => $this->mobileNumber,
            'storeLabel' => $this->storeLabel,
            'loyaltyNumber' => $this->loyaltyNumber,
            'referenceLabel' => $this->referenceLabel,
            'customerLabel' => $this->customerLabel,
            'terminalLabel' => $this->terminalLabel,
            'purposeOfTransaction' => $this->purposeOfTransaction,
            'additionalConsumerDataRequest' => $this->additionalConsumerDataRequest,
        ], fn($value) => $value !== null);
    }

    /**
     * Validate additional data field
     *
     * Ensures field is either null (not set) or a non-empty string within the maximum length.
     * Empty strings are rejected to prevent invalid QR codes, as the EMVCo specification
     * requires all present fields to have meaningful values.
     *
     * @param string|null $value The field value to validate
     * @param string $fieldName The field name for error messages
     * @throws ValidationException If value is an empty string
     * @throws InvalidLengthException If value exceeds maximum length
     */
    private function validateField(?string $value, string $fieldName): void
    {
        if ($value === null) {
            return;
        }

        $length = strlen($value);
        if ($length === 0) {
            throw new ValidationException("{$fieldName} cannot be empty");
        }

        if ($length > Specifications::MAX_ADDITIONAL_FIELD) {
            throw new InvalidLengthException(
                "{$fieldName} exceeds maximum length of " . Specifications::MAX_ADDITIONAL_FIELD . " characters (got {$length})",
            );
        }
    }
}
