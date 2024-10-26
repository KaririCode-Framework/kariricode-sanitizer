<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Exception;

use KaririCode\Exception\AbstractException;

final class SanitizationException extends AbstractException
{
    private const CODE_INVALID_INPUT = 4001;

    public static function invalidInput(string $expectedType): self
    {
        $message = sprintf(
            'Input must be a %s',
            $expectedType
        );

        return self::createException(
            self::CODE_INVALID_INPUT,
            'INVALID_INPUT',
            $message
        );
    }
}
