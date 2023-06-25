<?php declare(strict_types=1);

namespace SampleCode\Tests\Unit\Decimal;

use SampleCode\Decimal\Decimal;
use SampleCode\Decimal\DecimalFactory;
use SampleCode\Decimal\DecimalTransformer;
use SampleCode\Decimal\Exception\InvalidValueException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class DecimalFactoryTest extends TestCase {
    public function testGetReturnsDecimalWithCorrectValues(): void {
        $factory = new DecimalFactory($this->createMock(DecimalTransformer::class), new NullLogger());

        $decimal = $factory->get(4, 3, 1, true);

        $this->assertSame(4, $decimal->getCeilPart());
        $this->assertSame(3, $decimal->getFloatPart());
        $this->assertSame(1, $decimal->getFloatPrecision());
        $this->assertSame(true, $decimal->isPositive());
    }

    /** @dataProvider floatParametersDataProvider */
    public function testFromFloatCorrectTransformToAmountAndScale(
        float $float, int $ceilPart, int $floatPart, int $floatPrecision, bool $isPositive
    ): void {
        $factory = $this->getMockBuilder(DecimalFactory::class)
            ->setConstructorArgs([
                $this->createMock(DecimalTransformer::class),
                new NullLogger()
            ])
            ->onlyMethods(['get'])
            ->getMock();

        $factory->expects($this->once())
            ->method('get')
            ->with($ceilPart, $floatPart, $floatPrecision, $isPositive)
            ->willReturn($this->createMock(Decimal::class));

        $factory->fromFloat($float, $floatPrecision);
    }

    /** @dataProvider floatDataProvider */
    public function testCorrectFormatFromFloatAndToString(float $float): void {
        $factory = new DecimalFactory(new DecimalTransformer(), new NullLogger());
        $decimal = $factory->fromFloat($float);

        $this->assertSame($float, (float) $decimal->toString());
    }

    public function testFromFloatLogWarningIfPossibleRoundingDetect(): void {
        $factory = new DecimalFactory(
            $this->createMock(DecimalTransformer::class),
            $logger = $this->createMock(LoggerInterface::class),
        );
        $logger->expects($this->once())
            ->method('warning')
            ->withAnyParameters();

        $factory->fromFloat(0.00001, 5);
    }

    public function testFromStringReturnsCorrectDecimal(): void {
        $factory = $this->getMockBuilder(DecimalFactory::class)
            ->setConstructorArgs([
                $this->createMock(DecimalTransformer::class),
                new NullLogger()
            ])
            ->onlyMethods(['get'])
            ->getMock();

        $factory->expects($this->once())
            ->method('get')
            ->with(10, 5, 1, true)
            ->willReturn($decimal = $this->createMock(Decimal::class));


        $this->assertSame($decimal, $factory->fromString('10.5', 1));
    }

    public function testFromStringThrowsExceptionIfStringIsNotValid(): void {
        $factory = new DecimalFactory(
            $this->createMock(DecimalTransformer::class),
            $this->createMock(LoggerInterface::class),
        );

        $this->expectException(InvalidValueException::class);
        $factory->fromString('10.5.1', 1);
    }

    private function floatDataProvider(): array {
        return [
            [0],
            [0.1],
            [-0.01],
            [0.01],
            [-1.05],
            [1234.12],
            [-99999.99],
            [1000],
            [-1000],
            [10000000000000.45]
        ];
    }

    private function floatParametersDataProvider(): array {
        return [
            [0.1, 0, 1, 1, true],
            [0, 0, 0, 0, true],
            [-0.1, 0, 1, 1, false],
            [12.03, 12, 3, 2, true],
            [-12.03, -12, 3, 2, false],
        ];
    }
}
