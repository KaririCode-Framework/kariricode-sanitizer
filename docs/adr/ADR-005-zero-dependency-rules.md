# ADR-005: Zero-Dependency Rule Implementation

**Status:** Accepted
**Date:** 2026-03-04
**Component:** KaririCode\Sanitizer V3.2

## Context

All 33 built-in sanitization rules must be usable in any PHP 8.4 application without
pulling in third-party libraries. `HtmlPurifyRule` sounds like it might wrap a library
(e.g. HTMLPurifier), but the sanitizer is designed as a lightweight transform pipeline,
not a security-focused HTML filter library.

## Decision

Every rule in `src/Rule/**` is implemented using **only PHP built-in functions**.
No namespace under `src/Rule/` may `use` a class from outside `KaririCode\Sanitizer`.

| Rule | Built-in used |
|------|--------------|
| `TrimRule` | `trim()` |
| `HtmlEncodeRule` | `htmlspecialchars()` |
| `HtmlPurifyRule` | `strip_tags()` + `html_entity_decode()` |
| `StripTagsRule` | `strip_tags()` |
| `SlugRule` | `preg_replace()` + `mb_strtolower()` |
| `RoundRule` | `round()` / `ceil()` / `floor()` |
| `NormalizeDateRule` | `DateTimeImmutable` |
| `TimestampToDateRule` | `DateTimeImmutable` + `DateTimeZone` |
| `FormatCpfRule` | `preg_replace()` |

The sole exception is `AttributeSanitizer`, which depends on `kariricode/property-inspector`
(see ADR-002) — but that is an infrastructure class, not a rule.

## Alternatives Considered

- **HTMLPurifier for `HtmlPurifyRule`**: Feature-rich but adds a heavy dependency.
  Our use-case (basic XSS-safe output) is satisfied by `strip_tags` + entity decode.
- **`league/uri` for URL encoding**: Overkill — PHP's `urlencode()` / `rawurlencode()` suffice.

## Consequences

- `composer.json` `require` block stays minimal (only `kariricode/property-inspector`).
- Rules can be unit-tested in complete isolation with zero bootstrapping.
- Security-critical HTML sanitization (full XSS protection) should be handled by a
  dedicated library outside this package; `HtmlPurifyRule` is a convenience transform.
