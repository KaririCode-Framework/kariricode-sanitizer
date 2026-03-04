# ADR-001: Rule Registry Pattern

**Status:** Accepted
**Date:** 2026-03-04
**Component:** KaririCode\Sanitizer V3.2

## Context

The sanitizer must support many rule types (string, html, numeric, date, filter, type,
Brazilian) without coupling the engine to concrete implementations. The engine needs to
resolve rules by a short alias (`'trim'`, `'html.encode'`) at runtime, allow custom rules
to be registered, and work without a DI container.

## Decision

Use an alias-based in-memory registry implementing `RuleRegistry`:

```php
interface RuleRegistry
{
    public function register(string $alias, SanitizationRule $rule): void;
    public function resolve(string $alias): SanitizationRule;
    public function has(string $alias): bool;
    public function aliases(): array;
}
```

`InMemoryRuleRegistry` holds a `array<string, SanitizationRule>` map. The alias is
user-assigned (not derived from the rule itself), allowing multiple aliases for the
same class with different default parameters. `SanitizerServiceProvider` pre-registers
all 33 built-in rules using dot-notation namespaced aliases (e.g. `string.trim`,
`html.encode`).

## Alternatives Considered

- **Format-keyed registry** (like Serializer): Rejected — rules don't self-identify;
  the alias is meaningful to the caller, not intrinsic to the rule.
- **DI container binding**: Rejected — library must be container-agnostic (ARFA zero-dep).
- **Class-map resolution** (FQCN as key): Rejected — verbose at call site, prevents
  multiple configurations of the same class.

## Consequences

- Any rule can be registered under any alias; aliases are unique per registry.
- Custom rules integrate by calling `$registry->register('my.rule', new MyRule())`.
- Engine is fully decoupled from concrete rule classes.
- `SanitizerServiceProvider` is the single authoritative source of built-in aliases.
