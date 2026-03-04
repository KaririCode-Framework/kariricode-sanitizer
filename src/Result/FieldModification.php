<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Result;

/**
 * Records a single modification applied to a field.
 *
 * @package KaririCode\Sanitizer\Result
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 * @since   3.1.0 ARFA 1.3
 */
final readonly class FieldModification
{
    public function __construct(
        public string $field,
        public string $ruleName,
        public mixed $before,
        public mixed $after,
    ) {
    }

    public function wasModified(): bool
    {
        return $this->before !== $this->after;
    }
}
