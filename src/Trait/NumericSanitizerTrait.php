<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Trait;

trait NumericSanitizerTrait
{
    protected function extractNumbers(string $input): string
    {
        return preg_replace('/[^0-9]/', '', $input);
    }

    protected function preserveDecimalPoint(string $input, string $decimalPoint = '.'): string
    {
        $parts = explode($decimalPoint, $input);
        if (count($parts) > 2) {
            return $this->extractNumbers($parts[0]) . $decimalPoint . $this->extractNumbers(implode('', array_slice($parts, 1)));
        }

        return implode($decimalPoint, array_map([$this, 'extractNumbers'], $parts));
    }
}
