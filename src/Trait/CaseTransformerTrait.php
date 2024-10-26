<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Trait;

trait CaseTransformerTrait
{
    protected function toLowerCase(string $input): string
    {
        return strtolower($input);
    }

    protected function toUpperCase(string $input): string
    {
        return strtoupper($input);
    }

    protected function toCamelCase(string $input): string
    {
        // If already in camelCase, return without modifying
        if ($this->isAlreadyCamelCase($input)) {
            return $input;
        }

        // Remove extra underscores and normalize
        $input = trim($input, '_');
        $input = preg_replace('/_+/', '_', $input);

        // Convert to camelCase
        $input = strtolower($input);
        $output = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));

        return $output;
    }

    private function isAlreadyCamelCase(string $input): bool
    {
        return
            // Starts with a lowercase letter
            preg_match('/^[a-z]/', $input)
            // Contains at least one uppercase letter after the first position
            && preg_match('/[A-Z]/', substr($input, 1))
            // Does not contain underscores
            && !str_contains($input, '_')
            // Follows camelCase pattern (lowercase letter followed by uppercase)
            && preg_match('/^[a-z]+(?:[A-Z][a-z0-9]+)*$/', $input);
    }
}
