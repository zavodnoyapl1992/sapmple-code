<?php
declare(strict_types=1);

namespace SampleCode\Tests\Illuminati\Unit\Decimal;

use SampleCode\Decimal\Decimal;
use SampleCode\Decimal\DecimalTransformer;
use PHPUnit\Framework\TestCase;

class DecimalTransformerTest extends TestCase
{
    /** @dataProvider toStringParametersDataProvider */
    public function testToStringReturnCorrectValues(
        int $ceilPart, int $floatPart, int $floatPrecision, bool $isPositive, string $expected
    ): void
    {
        $transformer = new DecimalTransformer();
        $decimal = $this->createConfiguredMock(Decimal::class, [
            'getCeilPart' => $ceilPart,
            'getFloatPart' => $floatPart,
            'getFloatPrecision' => $floatPrecision,
            'isPositive' => $isPositive,
        ]);

        $this->assertSame($expected, $transformer->toString($decimal));
    }

    private function toStringParametersDataProvider(): array
    {
        return [
            [1, 0, 0, true, '1.0',],
            [1, 0, 0, false, '-1.0',],
            [0, 1, 1, true, '0.1',],
            [0, 1, 1, false, '-0.1',],
            [10, 2, 2, false, '-10.02',],
            [10, 2, 2, true, '10.02',],
            [0, 0, 0, false, '0',],
            [0, 0, 0, true, '0',],
            [0, 1, 8, true, '0.00000001',],
            [0, 1, 8, false, '-0.00000001',],
        ];
    }
}
