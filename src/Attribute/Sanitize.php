<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Attribute;

/**
 * Declares sanitization rules on a DTO property.
 *
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 *
 * @since   3.1.0 ARFA 1.3
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
final readonly class Sanitize
{
    /** @var list<string|array{0: string, 1: array<string, mixed>}> */
    public array $rules;

    /** @param list<string|array{0: string, 1: array<string, mixed>}> $rules */
    public function __construct(string|array ...$rules)
    {
        /** @var list<string|array{0: string, 1: array<string, mixed>}> $typed */
        $typed = array_values($rules);
        $this->rules = $typed;
    }
}
