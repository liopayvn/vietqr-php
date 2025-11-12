<?php

declare(strict_types=1);

namespace Liopay\VietQR\DTO;

/**
 * Parsed QR Data DTO
 *
 * Contains all parsed data from a QR code string
 *
 * @package Liopay\VietQR\DTO
 */
final class ParsedQRData
{
    private string $payloadFormat;
    private ?string $pointOfInitiation = null;
    private string $guid;
    private string $bankBin;
    private string $merchantId;
    private ?string $serviceCode = null;
    private ?string $merchantCategoryCode = null;
    private string $currency;
    private ?string $amount = null;
    private string $country;
    private ?string $merchantName = null;
    private ?string $merchantCity = null;
    private ?string $postalCode = null;
    private AdditionalDataField $additionalData;
    private string $crc;
    private string $qrType;

    public function __construct()
    {
        $this->additionalData = new AdditionalDataField();
    }

    // Setters
    public function setPayloadFormat(string $payloadFormat): self
    {
        $this->payloadFormat = $payloadFormat;
        return $this;
    }

    public function setPointOfInitiation(?string $pointOfInitiation): self
    {
        $this->pointOfInitiation = $pointOfInitiation;
        return $this;
    }

    public function setGUID(string $guid): self
    {
        $this->guid = $guid;
        return $this;
    }

    public function setBankBin(string $bankBin): self
    {
        $this->bankBin = $bankBin;
        return $this;
    }

    public function setMerchantId(string $merchantId): self
    {
        $this->merchantId = $merchantId;
        return $this;
    }

    public function setServiceCode(?string $serviceCode): self
    {
        $this->serviceCode = $serviceCode;
        return $this;
    }

    public function setMerchantCategoryCode(?string $mcc): self
    {
        $this->merchantCategoryCode = $mcc;
        return $this;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function setAmount(?string $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function setMerchantName(?string $merchantName): self
    {
        $this->merchantName = $merchantName;
        return $this;
    }

    public function setMerchantCity(?string $merchantCity): self
    {
        $this->merchantCity = $merchantCity;
        return $this;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function setAdditionalData(AdditionalDataField $additionalData): self
    {
        $this->additionalData = $additionalData;
        return $this;
    }

    public function setCRC(string $crc): self
    {
        $this->crc = $crc;
        return $this;
    }

    public function setQRType(string $qrType): self
    {
        $this->qrType = $qrType;
        return $this;
    }

    // Getters
    public function getPayloadFormat(): string
    {
        return $this->payloadFormat;
    }

    public function getPointOfInitiation(): ?string
    {
        return $this->pointOfInitiation;
    }

    public function getGUID(): string
    {
        return $this->guid;
    }

    public function getBankBin(): string
    {
        return $this->bankBin;
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    public function getServiceCode(): ?string
    {
        return $this->serviceCode;
    }

    public function getMerchantCategoryCode(): ?string
    {
        return $this->merchantCategoryCode;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getMerchantName(): ?string
    {
        return $this->merchantName;
    }

    public function getMerchantCity(): ?string
    {
        return $this->merchantCity;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getAdditionalData(): AdditionalDataField
    {
        return $this->additionalData;
    }

    public function getCRC(): string
    {
        return $this->crc;
    }

    public function getQRType(): string
    {
        return $this->qrType;
    }

    /**
     * Check if QR is static (point of initiation = "11") or dynamic ("12")
     */
    public function isStatic(): bool
    {
        return $this->pointOfInitiation === '11';
    }

    public function isDynamic(): bool
    {
        return $this->pointOfInitiation === '12';
    }
}
