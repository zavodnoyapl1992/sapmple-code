<?php
declare(strict_types=1);

namespace SampleCode\Decimal;

use JetBrains\PhpStorm\Pure;

class DecimalTransformer
{
    #[Pure] public function toString(Decimal $decimal): string {
        if ($decimal->getCeilPart() === 0 && $decimal->getFloatPrecision() === 0) {
            return '0';
        }
        return (
            ($decimal->isPositive() ? '' : '-')
            . ((string)$decimal->getCeilPart())
            . '.'
            . str_pad((string)$decimal->getFloatPart(), $decimal->getFloatPrecision(), '0', STR_PAD_LEFT));
    }
}
