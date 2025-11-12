<?php

declare(strict_types=1);

namespace Liopay\VietQR\ValueObject;

/**
 * Point of Initiation Value Object
 *
 * Represents whether QR is static (reusable) or dynamic (single-use)
 *
 * @package Liopay\VietQR\ValueObject
 */
final class PointOfInitiation
{
    public const STATIC = '11';
    public const DYNAMIC = '12';

    private string $value;

    /**
     * @param string $value Point of initiation value ('11' or '12')
     * @throws \InvalidArgumentException If value is invalid
     */
    public function __construct(string $value)
    {
        if (!in_array($value, [self::STATIC, self::DYNAMIC], true)) {
            throw new \InvalidArgumentException(
                "Invalid point of initiation: {$value}. Must be '11' (static) or '12' (dynamic)",
            );
        }

        $this->value = $value;
    }

    /**
     * Create static QR point of initiation
     *
     * @return self
     */
    public static function static(): self
    {
        return new self(self::STATIC);
    }

    /**
     * Create dynamic QR point of initiation
     *
     * @return self
     */
    public static function dynamic(): self
    {
        return new self(self::DYNAMIC);
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Check if this is static QR
     *
     * @return bool
     */
    public function isStatic(): bool
    {
        return $this->value === self::STATIC;
    }

    /**
     * Check if this is dynamic QR
     *
     * @return bool
     */
    public function isDynamic(): bool
    {
        return $this->value === self::DYNAMIC;
    }

    /**
     * Check equality
     *
     * @param PointOfInitiation $other
     * @return bool
     */
    public function equals(PointOfInitiation $other): bool
    {
        return $this->value === $other->value;
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
