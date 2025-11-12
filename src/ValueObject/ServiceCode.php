<?php

declare(strict_types=1);

namespace Liopay\VietQR\ValueObject;

use Liopay\VietQR\Constant\ServiceCodes;

/**
 * Service Code Value Object
 *
 * Immutable value object representing QR service type
 *
 * @package Liopay\VietQR\ValueObject
 */
final class ServiceCode
{
    private string $value;

    /**
     * @param string $value Service code value
     * @throws \InvalidArgumentException If service code is invalid
     */
    public function __construct(string $value)
    {
        if (!ServiceCodes::isValid($value)) {
            throw new \InvalidArgumentException("Invalid service code: {$value}");
        }

        $this->value = $value;
    }

    /**
     * Get service code value
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Check if equals another service code
     *
     * @param ServiceCode $other
     * @return bool
     */
    public function equals(ServiceCode $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Check if this is a payment service
     *
     * @return bool
     */
    public function isPayment(): bool
    {
        return $this->value === ServiceCodes::QRPUSH;
    }

    /**
     * Check if this is a cash withdrawal service
     *
     * @return bool
     */
    public function isCashWithdrawal(): bool
    {
        return $this->value === ServiceCodes::QRCASH;
    }

    /**
     * Check if this is an IBFT service
     *
     * @return bool
     */
    public function isIBFT(): bool
    {
        return $this->value === ServiceCodes::QRIBFTTC
            || $this->value === ServiceCodes::QRIBFTTA;
    }

    /**
     * Check if this is IBFT to card
     *
     * @return bool
     */
    public function isIBFTToCard(): bool
    {
        return $this->value === ServiceCodes::QRIBFTTC;
    }

    /**
     * Check if this is IBFT to account
     *
     * @return bool
     */
    public function isIBFTToAccount(): bool
    {
        return $this->value === ServiceCodes::QRIBFTTA;
    }

    /**
     * String representation
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
