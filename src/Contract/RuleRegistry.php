<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Contract;

/**
 * Registry mapping string aliases to SanitizationRule instances.
 *
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 *
 * @since   3.1.0 ARFA 1.3
 */
interface RuleRegistry
{
    public function register(string $alias, SanitizationRule $rule): void;

    public function resolve(string $alias): SanitizationRule;

    public function has(string $alias): bool;

    /** @return list<string> */
    public function aliases(): array;
}
