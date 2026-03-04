<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Contract;

/**
 * Core sanitization rule contract.
 *
 * Each rule transforms a value deterministically. Rules are stateless
 * and receive parameters via SanitizationContext.
 *
 * Unlike ValidationRule (which returns bool), SanitizationRule returns
 * the transformed value — the sanitized output.
 *
 * @package KaririCode\Sanitizer\Contract
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 * @since   3.1.0 ARFA 1.3
 */
interface SanitizationRule
{
    /**
     * Sanitize a value and return the cleaned result.
     *
     * Must be pure: same input + context → same output.
     * Must NOT throw exceptions for invalid input — return value as-is.
     */
    public function sanitize(mixed $value, SanitizationContext $context): mixed;

    /** Rule identifier for registry and logging. */
    public function getName(): string;
}
