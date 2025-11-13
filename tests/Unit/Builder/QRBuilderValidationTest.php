<?php

declare(strict_types=1);

namespace Liopay\VietQR\Tests\Unit\Builder;

use Liopay\VietQR\Builder\QRPushBuilder;
use Liopay\VietQR\Exception\{InvalidLengthException, ValidationException};
use PHPUnit\Framework\TestCase;

final class QRBuilderValidationTest extends TestCase
{
    public function testSetServiceCodeRejectsUnknownValue(): void
    {
        $builder = new QRPushBuilder();

        $this->expectException(ValidationException::class);
        $builder->setServiceCode('INVALID');
    }

    public function testSetCurrencyRequiresThreeDigits(): void
    {
        $builder = new QRPushBuilder();

        $this->expectException(ValidationException::class);
        $builder->setCurrency('70A');
    }

    public function testSetCurrencyRequiresExactLength(): void
    {
        $builder = new QRPushBuilder();

        $this->expectException(InvalidLengthException::class);
        $builder->setCurrency('70');
    }

    public function testSetCountryRequiresUppercaseAlpha(): void
    {
        $builder = new QRPushBuilder();

        $this->expectException(ValidationException::class);
        $builder->setCountry('vn');
    }
}
