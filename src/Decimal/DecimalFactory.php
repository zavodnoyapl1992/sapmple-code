<?php declare(strict_types=1);

namespace SampleCode\Decimal;

use SampleCode\Decimal\Exception\InvalidValueException;
use Psr\Log\LoggerInterface;

class DecimalFactory {
    private const DEFAULT_PRECISION = 2;

    public function __construct(
        private readonly DecimalTransformer $decimalTransformer,
        private readonly LoggerInterface    $logger,
    ) { }

    public function get(int $ceilPart, int $floatingPart, int $floatPrecision, bool $isPositive): Decimal {
        return new Decimal(
            $this->decimalTransformer,
            $ceilPart,
            $floatingPart,
            $floatPrecision,
            $isPositive
        );
    }

    /**
     * @throws InvalidValueException
     */
    public function fromFloat(float $float, int $precision = self::DEFAULT_PRECISION): Decimal {
        $floatAsString = number_format($float, $precision, '.', '');
        if ($floatAsString !== (string) $float) {
            $this->logger->warning("Possible rounding for {$float}, use {$floatAsString}");
        }

        return $this->fromString($floatAsString, $precision);
    }

    /**
     * @throws InvalidValueException
     */
    public function fromString(string $floatAsString, int $precision = self::DEFAULT_PRECISION): Decimal {
        if (!is_numeric($floatAsString)) {
            throw new InvalidValueException($floatAsString);
        }
        $floatParts = explode('.', $floatAsString);
        $ceilPart = $floatParts[0];
        $floatingPart = substr(rtrim($floatParts[1] ?? '', '0'), 0, $precision);

        return $this->get(
            (int) $ceilPart,
            (int) $floatingPart,
            strlen($floatingPart),
            ((float) $floatAsString) >= 0
        );
    }
}
