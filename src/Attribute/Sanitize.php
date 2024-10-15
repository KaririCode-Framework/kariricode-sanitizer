<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Attribute;

use KaririCode\Contract\Processor\ProcessableAttribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Sanitize implements ProcessableAttribute
{
    public function __construct(
        public readonly array $sanitizers,
        public readonly mixed $fallbackValue = null
    ) {
    }

    public function getProcessors(): array
    {
        return $this->sanitizers;
    }

    public function getFallbackValue(): mixed
    {
        return $this->fallbackValue;
    }
}
