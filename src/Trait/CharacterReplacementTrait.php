<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Trait;

trait CharacterReplacementTrait
{
    protected function replaceConsecutiveCharacters(string $input, string $char, string $replacement): string
    {
        return preg_replace('/' . preg_quote($char, '/') . '+/', $replacement, $input);
    }

    protected function replaceMultipleCharacters(string $input, array $replacements): string
    {
        return str_replace(array_keys($replacements), array_values($replacements), $input);
    }
}
