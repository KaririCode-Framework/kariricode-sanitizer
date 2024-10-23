<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Trait;

trait ValidationTrait
{
    protected function isNotEmpty(string $input): bool
    {
        return '' !== trim($input);
    }

    protected function isValidUtf8(string $input): bool
    {
        return mb_check_encoding($input, 'UTF-8');
    }

    protected function containsPattern(string $input, string $pattern): bool
    {
        return 1 === preg_match($pattern, $input);
    }
}
