<?php

declare(strict_types=1);

namespace Liopay\VietQR\Tests\Unit\DTO;

use Liopay\VietQR\DTO\AdditionalDataField;
use Liopay\VietQR\Exception\{InvalidLengthException, ValidationException};
use PHPUnit\Framework\TestCase;

final class AdditionalDataFieldTest extends TestCase
{
    public function testRejectsEmptyValues(): void
    {
        $additionalData = new AdditionalDataField();

        $this->expectException(ValidationException::class);
        $additionalData->setBillNumber('');
    }

    public function testRejectsValuesLongerThanAllowed(): void
    {
        $additionalData = new AdditionalDataField();

        $this->expectException(InvalidLengthException::class);
        $additionalData->setReferenceLabel(str_repeat('A', 26));
    }

    public function testStoresAdditionalConsumerDataRequest(): void
    {
        $additionalData = new AdditionalDataField();
        $additionalData->setAdditionalConsumerDataRequest('EXTRAINFO');

        $this->assertSame('EXTRAINFO', $additionalData->getAdditionalConsumerDataRequest());
    }

    public function testAcceptsMaximumLengthValue(): void
    {
        $additionalData = new AdditionalDataField();

        // Maximum allowed length is 25 characters
        $maxLengthValue = str_repeat('A', 25);
        $additionalData->setBillNumber($maxLengthValue);

        $this->assertSame($maxLengthValue, $additionalData->getBillNumber());
    }

    public function testRejectsValueExactlyAtMaxPlusOne(): void
    {
        $additionalData = new AdditionalDataField();

        // 26 characters should be rejected
        $this->expectException(InvalidLengthException::class);
        $additionalData->setReferenceLabel(str_repeat('A', 26));
    }
}
