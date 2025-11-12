<?php

declare(strict_types=1);

namespace Liopay\VietQR\DTO;

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

    public function setBillNumber(?string $billNumber): self
    {
        $this->billNumber = $billNumber;
        return $this;
    }

    public function setMobileNumber(?string $mobileNumber): self
    {
        $this->mobileNumber = $mobileNumber;
        return $this;
    }

    public function setStoreLabel(?string $storeLabel): self
    {
        $this->storeLabel = $storeLabel;
        return $this;
    }

    public function setLoyaltyNumber(?string $loyaltyNumber): self
    {
        $this->loyaltyNumber = $loyaltyNumber;
        return $this;
    }

    public function setReferenceLabel(?string $referenceLabel): self
    {
        $this->referenceLabel = $referenceLabel;
        return $this;
    }

    public function setCustomerLabel(?string $customerLabel): self
    {
        $this->customerLabel = $customerLabel;
        return $this;
    }

    public function setTerminalLabel(?string $terminalLabel): self
    {
        $this->terminalLabel = $terminalLabel;
        return $this;
    }

    public function setPurposeOfTransaction(?string $purposeOfTransaction): self
    {
        $this->purposeOfTransaction = $purposeOfTransaction;
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
            || $this->purposeOfTransaction !== null;
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
        ], fn($value) => $value !== null);
    }
}
