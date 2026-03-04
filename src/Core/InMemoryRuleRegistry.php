<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Core;

use KaririCode\Sanitizer\Contract\RuleRegistry;
use KaririCode\Sanitizer\Contract\SanitizationRule;
use KaririCode\Sanitizer\Exception\InvalidRuleException;

/**
 * In-memory rule registry with alias-to-rule mapping.
 *
 * @package KaririCode\Sanitizer\Core
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 * @since   3.1.0 ARFA 1.3
 */
final class InMemoryRuleRegistry implements RuleRegistry
{
    /** @var array<string, SanitizationRule> */
    private array $rules = [];

    #[\Override]
    public function register(string $alias, SanitizationRule $rule): void
    {
        if (isset($this->rules[$alias])) {
            throw InvalidRuleException::duplicateAlias($alias);
        }

        $this->rules[$alias] = $rule;
    }

    #[\Override]
    public function resolve(string $alias): SanitizationRule
    {
        return $this->rules[$alias] ?? throw InvalidRuleException::unknownAlias($alias);
    }

    #[\Override]
    public function has(string $alias): bool
    {
        return isset($this->rules[$alias]);
    }

    #[\Override]
    public function aliases(): array
    {
        return array_keys($this->rules);
    }
}
