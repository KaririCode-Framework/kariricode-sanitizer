<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Attribute;

/**
 * Declares sanitization rules on a DTO property.
 *
 * @package KaririCode\Sanitizer\Attribute
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 * @since   3.1.0 ARFA 1.3
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
final readonly class Sanitize
{
    /** @var list<string|array{0: string, 1: array<string, mixed>}> */
    public array $rules;

    public function __construct(string|array ...$rules)
    {
        $this->rules = array_values($rules);
    }
}
