<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Exception;

/**
 * @package KaririCode\Sanitizer\Exception
 * @since   3.1.0 ARFA 1.3
 */
final class SanitizationException extends \RuntimeException
{
    public static function engineError(string $message, ?\Throwable $previous = null): self
    {
        return new self("Sanitization engine error: {$message}", 0, $previous);
    }
}
