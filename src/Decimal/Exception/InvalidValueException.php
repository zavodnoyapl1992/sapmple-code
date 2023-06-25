<?php declare(strict_types=1);

namespace SampleCode\Decimal\Exception;

use JetBrains\PhpStorm\Immutable;
use Exception;

#[Immutable] class InvalidValueException extends Exception {
    public function __construct(string $value) {
        parent::__construct(sprintf('Value "%s" is not numeric', $value));
    }
}
