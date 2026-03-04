# ADR-004: ARFA 1.43 Rule Passthrough Contract

**Status:** Accepted
**Date:** 2026-03-04
**Component:** KaririCode\Sanitizer V3.2

## Context

PHP's type system is weak — a field annotated with `#[Sanitize('string.trim')]` could
receive an `int`, `null`, `array`, or any other type at runtime, especially when
processing unvalidated external input. Each rule must define what happens when the
incoming value type does not match the rule's expectation.

## Decision

ARFA 1.43 mandates a **passthrough contract**: a rule that cannot process the incoming
value type **must return the original value unchanged**. No exception, no coercion,
no null substitution.

```php
// Example: TrimRule — only operates on strings
public function sanitize(mixed $value, SanitizationContext $context): mixed
{
    if (!is_string($value)) {
        return $value;   // ← ARFA passthrough
    }
    // ... actual sanitization
}
```

This applies to all 33 built-in rules. Type guards are always the first statement in
`sanitize()`. The contract is verified by the conformance test suite
(`ArchitecturalContractTest`, `ImmutableStateTest`).

## Alternatives Considered

- **Throw `SanitizationException` on type mismatch**: Breaks pipelines unnecessarily —
  sanitization should be lenient, validation should enforce types.
- **Coerce to the expected type**: Implicit coercion changes meaning (e.g. `0` → `"0"`)
  and violates single-responsibility.
- **Return `null`**: Destroys original data silently.

## Consequences

- Rules are safe to apply to any `mixed` value at any pipeline stage.
- Sanitization and validation remain fully decoupled concerns.
- 100% branch coverage requires one test per rule with a non-matching type.
- Static analysis (`PHPStan`, `Psalm`) requires explicit type-narrowing before each
  `getParameter()` cast to satisfy strict mode type inference.
