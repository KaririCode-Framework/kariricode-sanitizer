<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Trait;

trait PatternFormatterTrait
{
    protected function applyPattern(string $input, string $pattern, string $placeholder = '#'): string
    {
        $inputChars = str_split($input);
        $patternChars = str_split($pattern);
        $result = '';
        $inputIndex = 0;

        foreach ($patternChars as $char) {
            if ($char === $placeholder && isset($inputChars[$inputIndex])) {
                $result .= $inputChars[$inputIndex++];
            } else {
                $result .= $char;
            }
        }

        return rtrim($result, $placeholder);
    }
}
