<?php declare(strict_types=1);

namespace SampleCode\Decimal;

use JetBrains\PhpStorm\Immutable;
use JetBrains\PhpStorm\Pure;
use Stringable;

#[Immutable] class Decimal implements Stringable {
    /** @psalm-var int<0, max> */
    private readonly int $ceilPart;
    /** @psalm-var int<0, max> */
    private readonly int $floatPart;
    /** @psalm-var int<0, max> */
    private readonly int $floatPrecision;

    public function __construct(
        private readonly DecimalTransformer $decimalTransformer,
        int                                 $ceilPart,
        int                                 $floatPart,
        int                                 $floatPrecision,
        private readonly bool               $isPositive
    ) {
        $this->ceilPart = abs($ceilPart);
        $this->floatPart = abs($floatPart);
        $this->floatPrecision = abs($floatPrecision);
    }

    #[Pure] public function toString(): string {
        return $this->decimalTransformer->toString($this);
    }

    #[Pure] public function getCeilPart(): int {
        return $this->ceilPart;
    }

    #[Pure] public function getFloatPart(): int {
        return $this->floatPart;
    }

    #[Pure] public function getFloatPrecision(): int {
        return $this->floatPrecision;
    }

    #[Pure] public function isPositive(): bool {
        return $this->isPositive || $this->isZero();
    }

    #[Pure] public function isZero(): bool {
        return $this->floatPart === 0 && $this->ceilPart === 0;
    }

    #[Pure] public function isNegative(): bool {
        return !$this->isPositive();
    }

    #[Pure] public function __toString(): string {
        return $this->toString();
    }
}
