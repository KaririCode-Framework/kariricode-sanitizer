# ADR-003: Sanitization Context Immutability

**Status:** Accepted
**Date:** 2026-03-04
**Component:** KaririCode\Sanitizer V3.2

## Context

Each rule invocation receives a `SanitizationContext` that carries the parameters
specific to that rule call (e.g. `max: 100`, `side: 'left'`). Multiple rules run
sequentially on a single field. If the context were mutable, a rule could
inadvertently modify parameters seen by subsequent rules.

## Decision

`SanitizationContextImpl` is declared `final readonly` — all properties are set at
construction and cannot be modified:

```php
final readonly class SanitizationContextImpl implements SanitizationContext
{
    public function __construct(private array $parameters = []) {}

    public function getParameter(string $key, mixed $default = null): mixed
    {
        return $this->parameters[$key] ?? $default;
    }
}
```

The engine creates a fresh `SanitizationContextImpl` per `(field, rule)` pair using the
parameters declared at rule registration. No shared mutable state exists between calls.

## Alternatives Considered

- **Mutable context with reset per rule**: More complex, risk of forgetting a reset.
- **Passing parameters directly to `sanitize(mixed, array)`**: Changes the `SanitizationRule`
  contract and leaks parameter-handling concern into every rule.

## Consequences

- Rules are stateless by design — identical input always produces identical output.
- Thread-safety is guaranteed without locks.
- Testing a rule requires only instantiating a `SanitizationContextImpl` with a parameter array.
- Parameter override between rules is impossible (by design).
