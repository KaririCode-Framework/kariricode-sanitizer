<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Exception;

/**
 * @package KaririCode\Sanitizer\Exception
 * @since   3.1.0 ARFA 1.3
 */
final class InvalidRuleException extends \InvalidArgumentException
{
    public static function duplicateAlias(string $alias): self
    {
        return new self("Sanitization rule alias '{$alias}' is already registered.");
    }

    public static function unknownAlias(string $alias): self
    {
        return new self("Sanitization rule alias '{$alias}' is not registered.");
    }
}
