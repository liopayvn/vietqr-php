<?php

declare(strict_types=1);

namespace Liopay\VietQR\DTO;

/**
 * Merchant Account Information (ID 38)
 *
 * @package Liopay\VietQR\DTO
 */
final class MerchantAccountInfo
{
    private string $guid;
    private string $bankBin;
    private string $merchantId;
    private ?string $serviceCode;

    public function __construct(
        string $guid,
        string $bankBin,
        string $merchantId,
        ?string $serviceCode = null
    ) {
        $this->guid = $guid;
        $this->bankBin = $bankBin;
        $this->merchantId = $merchantId;
        $this->serviceCode = $serviceCode;
    }

    public function getGuid(): string
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

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'guid' => $this->guid,
            'bankBin' => $this->bankBin,
            'merchantId' => $this->merchantId,
            'serviceCode' => $this->serviceCode,
        ];
    }
}
