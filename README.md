# KaririCode Sanitizer

<div align="center">

[![CI](https://github.com/KaririCode-Framework/kariricode-sanitizer/actions/workflows/ci.yml/badge.svg)](https://github.com/KaririCode-Framework/kariricode-sanitizer/actions/workflows/ci.yml)
[![PHP 8.4+](https://img.shields.io/badge/PHP-8.4%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-22c55e.svg)](LICENSE)
[![PHPStan Level 9](https://img.shields.io/badge/PHPStan-Level%209-4F46E5)](https://phpstan.org/)
[![Tests](https://img.shields.io/badge/Tests-175%20passing-22c55e)](https://github.com/KaririCode-Framework/kariricode-sanitizer/actions)
[![Coverage](https://img.shields.io/badge/Coverage-100%25-22c55e)](https://github.com/KaririCode-Framework/kariricode-sanitizer/actions)
[![Rules](https://img.shields.io/badge/Rules-33-22c55e)](docs/spec/SPEC-002-rule-reference.md)
[![ARFA](https://img.shields.io/badge/ARFA-1.43-orange)](https://kariricode.org)
[![KaririCode Framework](https://img.shields.io/badge/KaririCode-Framework-orange)](https://kariricode.org)

**Composable, rule-based data sanitization engine for PHP 8.4+ — 33 rules, zero dependencies.**

[Installation](#installation) · [Quick Start](#quick-start) · [Attribute DTO](#attribute-driven-dto-sanitization) · [All Rules](#all-33-rules) · [Architecture](#architecture) · [Docs](docs/README.md)

</div>

---

## The Problem

Raw user input arrives dirty — whitespace, wrong case, dangerous HTML, unformatted documents — and cleaning it is always ad-hoc:

```php
// Sprinkled everywhere with no audit trail
$name  = ucwords(strtolower(trim($request->name)));
$email = strtolower(trim($request->email));
$cpf   = preg_replace('/\D/', '', $request->cpf);
$bio   = htmlspecialchars(strip_tags($request->bio));

// No record of what changed, no idempotency guarantee,
// no attribute-driven DTOs, no composition.
```

## The Solution

```php
use KaririCode\Sanitizer\Provider\SanitizerServiceProvider;

$engine = (new SanitizerServiceProvider())->createEngine();

$result = $engine->sanitize(
    data: [
        'name'  => '  walmir  SILVA  ',
        'email' => '  Admin@Kariricode.ORG  ',
        'cpf'   => '52998224725',
        'bio'   => '<script>alert("xss")</script><b>Bold</b>',
    ],
    fieldRules: [
        'name'  => ['trim', 'normalize_whitespace', 'capitalize'],
        'email' => ['trim', 'lower_case', 'email_filter'],
        'cpf'   => ['format_cpf'],
        'bio'   => ['strip_tags', 'html_encode'],
    ],
);

echo $result->get('name');  // "Walmir Silva"
echo $result->get('email'); // "admin@kariricode.org"
echo $result->get('cpf');   // "529.982.247-25"
echo $result->get('bio');   // "&lt;script&gt;...Bold"
```

---

## Requirements

| Requirement | Version |
|---|---|
| PHP | 8.4 or higher |
| kariricode/property-inspector | ^2.0 |

---

## Installation

```bash
composer require kariricode/sanitizer
```

---

## Quick Start

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use KaririCode\Sanitizer\Provider\SanitizerServiceProvider;

$engine = (new SanitizerServiceProvider())->createEngine();

$result = $engine->sanitize(
    data: ['name' => '  walmir  SILVA  ', 'email' => '  Admin@Example.ORG  '],
    fieldRules: [
        'name'  => ['trim', 'normalize_whitespace', 'capitalize'],
        'email' => ['trim', 'lower_case', 'email_filter'],
    ],
);

echo $result->get('name');  // "Walmir Silva"
echo $result->get('email'); // "admin@example.org"
```

---

## Attribute-Driven DTO Sanitization

```php
use KaririCode\Sanitizer\Attribute\Sanitize;
use KaririCode\Sanitizer\Provider\SanitizerServiceProvider;

final class CreateUserRequest
{
    #[Sanitize('trim', 'lower_case', 'email_filter')]
    public string $email = '  User@Test.COM  ';

    #[Sanitize('trim', 'capitalize')]
    public string $name = '  walmir silva  ';

    #[Sanitize('format_cpf')]
    public string $cpf = '52998224725';

    #[Sanitize(['truncate', ['max' => 200, 'suffix' => '…']])]
    public string $bio = '';
}

$sanitizer = (new SanitizerServiceProvider())->createAttributeSanitizer();
$dto       = new CreateUserRequest();
$sanitizer->sanitize($dto);

// $dto->email === 'user@test.com'
// $dto->name  === 'Walmir Silva'
// $dto->cpf   === '529.982.247-25'
```

---

## Modification Tracking

Every change is logged with before/after values — full audit trail for free:

```php
$result = $engine->sanitize(
    ['name' => '  Walmir  '],
    ['name' => ['trim', 'upper_case']],
);

$result->wasModified();        // true
$result->modifiedFields();     // ['name']
$result->modificationCount();  // 2

foreach ($result->modificationsFor('name') as $mod) {
    echo "{$mod->ruleName}: '{$mod->before}' → '{$mod->after}'\n";
}
// trim: '  Walmir  ' → 'Walmir'
// upper_case: 'Walmir' → 'WALMIR'
```

---

## XSS Prevention

```php
$result = $engine->sanitize(
    ['input' => '<script>alert("xss")</script><b>Bold</b>'],
    ['input' => ['strip_tags', 'html_encode']],
);
// Result: "&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;Bold"
// strip_tags alone: 'alert("xss")Bold'
// html_purify (strip + entity decode + trim): 'Bold'
```

---

## Brazilian Document Formatting

```php
$result = $engine->sanitize(
    ['cpf' => '52998224725', 'cnpj' => '11222333000181', 'cep' => '63100000'],
    ['cpf' => ['format_cpf'], 'cnpj' => ['format_cnpj'], 'cep' => ['format_cep']],
);
// cpf:  "529.982.247-25"
// cnpj: "11.222.333/0001-81"
// cep:  "63100-000"
```

---

## All 33 Rules

| Category | Count | Aliases |
|---|---|---|
| **String** | 12 | `trim`, `lower_case`, `upper_case`, `capitalize`, `slug`, `truncate`, `normalize_whitespace`, `normalize_line_endings`, `pad`, `replace`, `regex_replace`, `strip_non_printable` |
| **HTML** | 5 | `strip_tags`, `html_encode`, `html_decode`, `html_purify`, `url_encode` |
| **Numeric** | 4 | `to_int`, `to_float`, `clamp`, `round` |
| **Type** | 3 | `to_bool`, `to_string`, `to_array` |
| **Date** | 2 | `normalize_date`, `timestamp_to_date` |
| **Filter** | 4 | `digits_only`, `alpha_only`, `alphanumeric_only`, `email_filter` |
| **Brazilian** | 3 | `format_cpf`, `format_cnpj`, `format_cep` |

See [SPEC-002](docs/spec/SPEC-002-rule-reference.md) for full parameter reference.

---

## Rule Parameters

```php
// truncate — max chars + suffix
$engine->sanitize(['bio' => $bio], ['bio' => [['truncate', ['max' => 200, 'suffix' => '…']]]]);

// pad — length, pad char, side ('left'|'right'|'both')
$engine->sanitize(['id' => '7'], ['id' => [['pad', ['length' => 5, 'pad' => '0', 'side' => 'left']]]]);
// → "00007"

// round — precision and mode ('round'|'ceil'|'floor')
$engine->sanitize(['price' => 9.9], ['price' => [['round', ['precision' => 2]]]]);

// clamp — min and max bounds
$engine->sanitize(['age' => 150], ['age' => [['clamp', ['min' => 0, 'max' => 120]]]]);

// normalize_date — from/to format
$engine->sanitize(['dob' => '25/12/1990'], ['dob' => [['normalize_date', ['from' => 'd/m/Y', 'to' => 'Y-m-d']]]]);
// → "1990-12-25"
```

---

## Custom Rules

```php
use KaririCode\Sanitizer\Contract\SanitizationRule;
use KaririCode\Sanitizer\Contract\SanitizationContext;

final class PhoneRule implements SanitizationRule
{
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (!is_string($value)) {
            return $value;   // ARFA passthrough — do not coerce
        }
        return preg_replace('/\D/', '', $value) ?? $value;
    }

    #[\Override]
    public function getName(): string
    {
        return 'phone';
    }
}

// Register and use
$registry = (new SanitizerServiceProvider())->createRegistry();
$registry->register('phone', new PhoneRule());

$engine = new SanitizerEngine($registry);
$result = $engine->sanitize(['phone' => '(85) 99999-9999'], ['phone' => ['phone']]);
// → "85999999999"
```

---

## Ecosystem Position

```
DPO Pipeline:     Validator → ★ Sanitizer ★ → Transformer → Business Logic
Infra Pipeline:   Object ↔ Normalizer ↔ Array ↔ Serializer ↔ String
Cross-Layer:      Request DTO ↔ Mapper ↔ Domain Entity ↔ Mapper ↔ Response DTO
```

The Sanitizer **cleans data** — removes noise while preserving semantic meaning.
Key property: idempotency — `sanitize(sanitize(x)) = sanitize(x)`.
Contrast with the Transformer, which converts representation and may change type.

---

## Architecture

### Source layout

```
src/
├── Attribute/       Sanitize — field-level sanitization annotation
├── Configuration/   SanitizerConfiguration
├── Contract/        SanitizationRule · SanitizationContext · RuleRegistry
├── Core/            SanitizerEngine · SanitizationContextImpl · InMemoryRuleRegistry
│                    SanitizeAttributeHandler · AttributeSanitizer
├── Event/           SanitizationStartedEvent · SanitizationCompletedEvent
├── Exception/       SanitizationException · InvalidRuleException
├── Integration/     ProcessorBridge
├── Provider/        SanitizerServiceProvider
├── Result/          SanitizationResult · FieldModification
└── Rule/
    ├── Brazilian/   FormatCPF · FormatCNPJ · FormatCEP
    ├── Date/        NormalizeDate · TimestampToDate
    ├── Filter/      DigitsOnly · AlphaOnly · AlphanumericOnly · EmailFilter
    ├── Html/        StripTags · HtmlEncode · HtmlDecode · HtmlPurify · UrlEncode
    ├── Numeric/     ToInt · ToFloat · Clamp · Round
    ├── String/      Trim · LowerCase · UpperCase · Capitalize · Slug · Truncate · …
    └── Type/        ToBool · ToString · ToArray
```

### Key design decisions

| Decision | Rationale | ADR |
|---|---|---|
| Alias-based rule registry | Flat names (`trim`), no FQCN coupling, custom aliases | [ADR-001](docs/adr/ADR-001-rule-registry-pattern.md) |
| Property Inspector integration | Delegates reflection and caching to `kariricode/property-inspector` | [ADR-002](docs/adr/ADR-002-property-inspector-integration.md) |
| Immutable `SanitizationContext` | Thread safety, no cross-rule parameter pollution | [ADR-003](docs/adr/ADR-003-sanitization-context-immutability.md) |
| ARFA passthrough contract | Non-matching types returned unchanged — rules never coerce | [ADR-004](docs/adr/ADR-004-arfa-passthrough-contract.md) |
| Zero-dependency rules | All 33 rules use only PHP built-ins | [ADR-005](docs/adr/ADR-005-zero-dependency-rules.md) |

### Specifications

| Spec | Covers |
|---|---|
| [SPEC-001](docs/spec/SPEC-001-sanitizer-engine.md) | Engine contract, sanitize flow, result API |
| [SPEC-002](docs/spec/SPEC-002-rule-reference.md) | All 33 rules — aliases, parameters, defaults |
| [SPEC-003](docs/spec/SPEC-003-attribute-sanitizer.md) | `#[Sanitize]` attribute shape and DTO flow |

---

## Project Stats

| Metric | Value |
|---|---|
| PHP source files | 51 |
| Source lines | ~2,100 |
| Test files | 20 |
| Test lines | ~1,938 |
| Tests | 175 passing |
| Assertions | 425 |
| Coverage | 100% (48 classes) |
| External runtime dependencies | 1 (`kariricode/property-inspector`) |
| Rule classes | 33 |
| Rule categories | 7 |
| PHPStan level | 9 (0 errors) |
| Psalm | 100% type inference (0 errors) |
| PHP version | 8.4+ |
| ARFA compliance | 1.43 V4.0 |

---

## Contributing

```bash
git clone https://github.com/KaririCode-Framework/kariricode-sanitizer.git
cd kariricode-sanitizer
composer install
kcode init
kcode quality  # Must pass before opening a PR
```

---

## License

[MIT License](LICENSE) © [Walmir Silva](mailto:community@kariricode.org)

---

<div align="center">

Part of the **[KaririCode Framework](https://kariricode.org)** ecosystem.

[kariricode.org](https://kariricode.org) · [GitHub](https://github.com/KaririCode-Framework/kariricode-sanitizer) · [Packagist](https://packagist.org/packages/kariricode/sanitizer) · [Issues](https://github.com/KaririCode-Framework/kariricode-sanitizer/issues)

</div>
