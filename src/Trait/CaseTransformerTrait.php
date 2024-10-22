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
        $input = $this->toLowerCase($input);

        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));
    }
}
