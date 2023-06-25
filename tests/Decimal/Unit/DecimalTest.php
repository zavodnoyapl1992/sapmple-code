<?php declare(strict_types=1);

namespace SampleCode\Tests\Unit\Decimal;

use SampleCode\Decimal\Decimal;
use SampleCode\Decimal\DecimalTransformer;
use PHPUnit\Framework\TestCase;

class DecimalTest extends TestCase {
    /** @dataProvider floatParametersDataProvider */
    public function testToStringReturnsCorrectFloatAsStringValue(int $ceilPart, int $floatPart, int $floatPrecision, bool $isPositive, string $expectedValue): void {
        $decimal = new Decimal(new DecimalTransformer(), $ceilPart, $floatPart, $floatPrecision, $isPositive);

        $this->assertSame($expectedValue, $decimal->toString());
    }

    private function floatParametersDataProvider(): array {
        return [
            [1, 0, 0, true, '1.0',],
            [1, 0, 0, false, '-1.0',],
            [0, 1, 1, true, '0.1',],
            [0, 1, 1, false, '-0.1',],
            [10, 2, 2, false, '-10.02',],
            [10, 2, 2, true, '10.02',],
            [-10, 2, 2, false, '-10.02',],
            [-10, 2, 2, true, '10.02',],
            [0, 0, 0, false, '0',],
            [0, 0, 0, true, '0',],
        ];
    }

    public function testToStringCorrectCall() {
        $decimal = new Decimal(
            $decimalTransformer = $this->createMock(DecimalTransformer::class), 10, 5, 1, true
        );
        $decimalTransformer->expects($this->once())
            ->method('toString')
            ->with($decimal)
            ->willReturn('0.1');

        $this->assertSame('0.1', $decimal->toString());
    }

    public function test__toStringReturnsCorrectFloatAsStringValue(): void {
        $decimal = $this->getMockBuilder(Decimal::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['toString'])
            ->getMock();

        $decimal->expects($this->once())
            ->method('toString')
            ->willReturn($stringResult = '1.00');

        $this->assertSame($stringResult, (string) $decimal);
    }

    /**
     * @dataProvider isZeroDataProvider
     */
    public function testIsZeroReturnCorrectValue(bool $isPositive, int $ceilPart, int $floatPart, bool $isZero): void {
        $decimal = new Decimal($this->createMock(DecimalTransformer::class), $ceilPart, $floatPart, 2, $isPositive);

        $this->assertSame($isZero, $decimal->isZero());
    }

    private function isZeroDataProvider(): array {
        return [
            [true, 0, 0, true],
            [false, 0, 0, true],
            [true, 1, 0, false],
            [true, 0, 1, false],
            [false, 1, 0, false],
            [false, 0, 1, false],
        ];
    }

    /**
     * @dataProvider isPositiveDataProvider
     */
    public function testIsPositiveReturnCorrectValue(bool $isPositive, int $ceilPart, int $floatPart, bool $isPositiveResult): void {
        $decimal = new Decimal($this->createMock(DecimalTransformer::class), $ceilPart, $floatPart, 2, $isPositive);

        $this->assertSame($isPositiveResult, $decimal->isPositive());
    }

    private function isPositiveDataProvider(): array {
        return [
            [true, 0, 0, true],
            [false, 0, 0, true],
            [true, 1, 0, true],
            [true, 0, 1, true],
            [false, 1, 0, false],
            [false, 0, 1, false],
        ];
    }

    /**
     * @dataProvider isNegativeDataProvider
     */
    public function testIsNegativeReturnCorrectValue(bool $isPositive): void {
        $decimal = $this->getMockBuilder(Decimal::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isPositive'])
            ->getMock();

        $decimal->expects($this->once())
            ->method('isPositive')
            ->willReturn($isPositive);

        $this->assertSame(!$isPositive, $decimal->isNegative());
    }

    private function isNegativeDataProvider(): array {
        return [
            [true],
            [false],
        ];
    }
}
