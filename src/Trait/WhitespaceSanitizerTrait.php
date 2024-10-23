<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Trait;

trait WhitespaceSanitizerTrait
{
    protected function removeAllWhitespace(string $input): string
    {
        return preg_replace('/\s+/', '', $input);
    }

    protected function normalizeWhitespace(string $input): string
    {
        return preg_replace('/\s+/', ' ', $input);
    }

    protected function trimWhitespace(string $input): string
    {
        return trim($input);
    }
}
