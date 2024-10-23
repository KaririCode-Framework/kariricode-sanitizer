<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Trait;

trait CharacterFilterTrait
{
    protected function filterAllowedCharacters(string $input, string $allowed): string
    {
        return preg_replace('/[^' . preg_quote($allowed, '/') . ']/', '', $input);
    }

    protected function keepOnlyAlphanumeric(string $input, array $additionalChars = []): string
    {
        $pattern = '/[^a-zA-Z0-9' . preg_quote(implode('', $additionalChars), '/') . ']/';

        return preg_replace($pattern, '', $input);
    }
}
