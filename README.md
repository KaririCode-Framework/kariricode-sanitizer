# KaririCode Sanitizer

<div align="center">

[![PHP 8.4+](https://img.shields.io/badge/PHP-8.4%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-22c55e.svg)](LICENSE)
[![PHPStan Level 9](https://img.shields.io/badge/PHPStan-Level%209-4F46E5)](https://phpstan.org/)
[![Rules](https://img.shields.io/badge/Rules-33-22c55e)](https://kariricode.org)
[![Zero Dependencies](https://img.shields.io/badge/Dependencies-0-22c55e)](composer.json)
[![ARFA](https://img.shields.io/badge/ARFA-1.3-orange)](https://kariricode.org)
[![KaririCode Framework](https://img.shields.io/badge/KaririCode-Framework-orange)](https://kariricode.org)

**Composable, rule-based data sanitization engine for PHP 8.4+ — 33 rules, zero dependencies.**

[Installation](#installation) · [Quick Start](#quick-start) · [XSS Prevention](#xss-prevention) · [All Rules](#all-33-rules) · [Architecture](#architecture)

</div>

---

## The Problem

Raw user input arrives dirty — whitespace, wrong case, dangerous HTML, unformatted documents — and cleaning it is always ad-hoc:

```php
// Sprinkled everywhere with no audit trail
$name  = ucwords(strtolower(trim($request->name)));
$email = strtolower(trim($request->email));
$cpf   = preg_replace('/\D/', '', $request->cpf);
$input = htmlspecialchars(strip_tags($request->bio));

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
echo $result->get('bio');   // "&lt;script&gt;alert(...)...Bold"
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
        'email' => ['trim', 'lower_case'],
    ],
);

echo $result->get('name');  // "Walmir Silva"
echo $result->get('email'); // "admin@example.org"
```

---

## Attribute-Driven DTO Sanitization

```php
use KaririCode\Sanitizer\Attribute\Sanitize;

final class CreateUserRequest
{
    #[Sanitize('trim', 'lower_case')]
    public string $email = '  User@Test.COM  ';

    #[Sanitize('trim', 'capitalize')]
    public string $name = '  walmir silva  ';

    #[Sanitize('format_cpf')]
    public string $cpf = '52998224725';
}

$sanitizer = (new SanitizerServiceProvider())->createAttributeSanitizer();
$result    = $sanitizer->sanitize(new CreateUserRequest());

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
// string.trim: '  Walmir  ' → 'Walmir'
// string.upper_case: 'Walmir' → 'WALMIR'
```

---

## XSS Prevention

```php
$result = $engine->sanitize(
    ['input' => '<script>alert("xss")</script><b>Bold</b>'],
    ['input' => ['strip_tags', 'html_encode']],
);
// Result: "&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;Bold"
// Or with strip_tags alone: 'alert("xss")Bold'
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

| Category | Rules | Aliases |
|---|---|---|
| **String** (12) | Trim, LowerCase, UpperCase, Capitalize, Slug, Truncate, NormalizeWhitespace, NormalizeLineEndings, Pad, Replace, RegexReplace, StripNonPrintable | `trim`, `lower_case`, `upper_case`, `capitalize`, `slug`, `truncate`, `normalize_whitespace`, `normalize_line_endings`, `pad`, `replace`, `regex_replace`, `strip_non_printable` |
| **HTML** (5) | StripTags, HtmlEncode, HtmlDecode, HtmlPurify, UrlEncode | `strip_tags`, `html_encode`, `html_decode`, `html_purify`, `url_encode` |
| **Numeric** (4) | ToInt, ToFloat, Clamp, Round | `to_int`, `to_float`, `clamp`, `round` |
| **Type** (3) | ToBool, ToString, ToArray | `to_bool`, `to_string`, `to_array` |
| **Date** (2) | NormalizeDate, TimestampToDate | `normalize_date`, `timestamp_to_date` |
| **Filter** (4) | DigitsOnly, AlphaOnly, AlphanumericOnly, EmailFilter | `digits_only`, `alpha_only`, `alphanumeric_only`, `email_filter` |
| **Brazilian** (3) | FormatCPF, FormatCNPJ, FormatCEP | `format_cpf`, `format_cnpj`, `format_cep` |

---

## Engine API (Programmatic)

```php
$engine = (new SanitizerServiceProvider())->createEngine();

$result = $engine->sanitize(
    ['html' => '<b>test</b>', 'text' => '  spaces  '],
    ['html' => ['strip_tags', 'trim'], 'text' => ['trim', 'upper_case']],
);

$result->get('html');              // "test"
$result->get('text');              // "SPACES"
$result->wasModified();            // true
$result->modifiedFields();         // ['html', 'text']
$result->modificationCount();      // 4

foreach ($result->modificationsFor('html') as $mod) {
    echo "{$mod->ruleName}: '{$mod->before}' → '{$mod->after}'\n";
}
// html.strip_tags: '<b>test</b>' → 'test'
```

---

## Ecosystem Position

```
DPO Pipeline:     Validator → ★ Sanitizer ★ → Transformer → Business Logic
Infra Pipeline:   Object ↔ Normalizer ↔ Array ↔ Serializer ↔ String
Cross-Layer:      Request DTO ↔ Mapper ↔ Domain Entity ↔ Mapper ↔ Response DTO
```

The Sanitizer **cleans data** — removes noise while preserving semantic meaning. Key property: idempotency — `sanitize(sanitize(x)) = sanitize(x)`. Contrast with the Transformer, which converts representation and may change type.

---

## Architecture

### Source layout

```
src/
├── Attribute/       Sanitize — field-level sanitization annotation
├── Contract/        SanitizationRule · SanitizationContext · SanitizerEngine · Modification
├── Core/            SanitizerEngine · SanitizationContextImpl · InMemoryRuleRegistry
├── Exception/       SanitizationException · InvalidRuleException
├── Provider/        SanitizerServiceProvider — factory for engine & attribute sanitizer
└── Rule/
    ├── Brazilian/   FormatCPF · FormatCNPJ · FormatCEP
    ├── Date/        NormalizeDate · TimestampToDate
    ├── Filter/      DigitsOnly · AlphaOnly · AlphanumericOnly · EmailFilter
    ├── HTML/        StripTags · HtmlEncode · HtmlDecode · HtmlPurify · UrlEncode
    ├── Numeric/     ToInt · ToFloat · Clamp · Round
    ├── String/      Trim · LowerCase · UpperCase · Capitalize · Slug · Truncate · …
    └── Type/        ToBool · ToString · ToArray
```

### Key design decisions

| Decision | Rationale | ADR |
|---|---|---|
| Idempotency guarantee | `sanitize(sanitize(x)) = sanitize(x)` for all rules | [ADR-001](docs/adr/ADR-001-idempotency.md) |
| Modification tracking | Full audit trail without extra overhead | [ADR-002](docs/adr/ADR-002-modification-tracking.md) |
| `final readonly` rules | Immutability, PHPStan L9 | [ADR-003](docs/adr/ADR-003-immutable-rules.md) |

### Specifications

| Spec | Covers |
|---|---|
| [SPEC-001](docs/spec/SPEC-001-sanitization-contract.md) | Rule contract and idempotency |
| [SPEC-002](docs/spec/SPEC-002-modification-tracking.md) | Modification record format |

---

## Project Stats

| Metric | Value |
|---|---|
| PHP source files | 50 |
| Source lines | 1,913 |
| Test files | 15 |
| Test lines | 969 |
| External runtime dependencies | 1 (kariricode/property-inspector) |
| Rule classes | 33 |
| Rule categories | 7 |
| PHPStan level | 9 |
| PHP version | 8.4+ |
| ARFA compliance | 1.3 |

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
