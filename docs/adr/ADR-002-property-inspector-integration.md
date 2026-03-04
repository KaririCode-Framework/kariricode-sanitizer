# ADR-002: Property Inspector Integration

**Status:** Accepted
**Date:** 2026-03-04
**Component:** KaririCode\Sanitizer V3.2

## Context

`AttributeSanitizer` must scan PHP objects for `#[Sanitize]` attributes on properties,
extract rule definitions, apply the sanitizer engine, and write back results. The naive
approach uses raw `ReflectionClass` loops in the sanitizer itself — duplicating
reflection infrastructure that already exists across the KaririCode ecosystem.

## Decision

Adopt `kariricode/property-inspector ^2.0` as the sole reflection dependency.
`AttributeSanitizer` composes a `PropertyInspector` configured with `AttributeAnalyzer(Sanitize::class)`:

```php
$inspector = new PropertyInspector(new AttributeAnalyzer(Sanitize::class));
$handler   = $inspector->inspect($object, new SanitizeAttributeHandler());
```

`SanitizeAttributeHandler` implements `PropertyAttributeHandler` — it receives each
property + attribute, accumulates field rules and current values, then exposes
`getFieldRules()` and `getProcessedPropertyValues()` for the engine call.
After sanitization, `applyChanges()` writes results back to the object via reflection.

## Alternatives Considered

- **Inline Reflection loops**: Simpler initially, but duplicates caching and error
  handling logic already solved by `property-inspector`.
- **Symfony PropertyAccess**: External framework coupling — violates zero-dep ADR.

## Consequences

- Reflection caching and `ReflectionException` handling are delegated to the library.
- `SanitizeAttributeHandler` is the only class aware of the `#[Sanitize]` shape.
- Adding new attribute parameters requires only changing `SanitizeAttributeHandler`,
  not the engine or inspector.
- `composer.json` gains one library dependency (`kariricode/property-inspector`).
