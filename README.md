# KaririCode\Sanitizer

**Composable, rule-based data sanitization engine for PHP 8.4+ — 33 rules, zero dependencies.**

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.4-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![ARFA](https://img.shields.io/badge/ARFA-1.3-orange)]()
[![Rules](https://img.shields.io/badge/rules-33-brightgreen)]()

Part of the [KaririCode Framework](https://github.com/kariricode) processing ecosystem.

## Why KaririCode\Sanitizer

- **33 built-in rules** across 7 categories — String, HTML, Numeric, Type, Date, Filter, Brazilian
- **Zero external dependencies** — pure PHP 8.4+
- **Same architecture as KaririCode\Validator** — consistent DPO pipeline
- **Modification tracking** — every change logged with before/after values
- **Attribute-driven DTOs** — `#[Sanitize]` on properties for declarative sanitization
- **Pipeline composition** — rules chain sequentially per field

## Installation

```bash
composer require kariricode/sanitizer
```

## Quick Start

```php
use KaririCode\Sanitizer\Provider\SanitizerServiceProvider;

$engine = (new SanitizerServiceProvider())->createEngine();

$result = $engine->sanitize(
    data: [
        'name'  => '  walmir  SILVA  ',
        'email' => '  Admin@Kariricode.ORG  ',
        'cpf'   => '52998224725',
    ],
    fieldRules: [
        'name'  => ['trim', 'normalize_whitespace', 'capitalize'],
        'email' => ['trim', 'lower_case', 'email_filter'],
        'cpf'   => ['format_cpf'],
    ],
);

echo $result->get('name');  // "Walmir Silva"
echo $result->get('email'); // "admin@kariricode.org"
echo $result->get('cpf');   // "529.982.247-25"
```

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
$result = $sanitizer->sanitize(new CreateUserRequest());

// $dto->email === 'user@test.com'
// $dto->name  === 'Walmir Silva'
// $dto->cpf   === '529.982.247-25'
```

## Modification Tracking

```php
$result = $engine->sanitize(
    ['name' => '  Walmir  '],
    ['name' => ['trim', 'upper_case']],
);

$result->wasModified();           // true
$result->modifiedFields();        // ['name']
$result->modificationCount();     // 2

foreach ($result->modificationsFor('name') as $mod) {
    echo "{$mod->ruleName}: '{$mod->before}' → '{$mod->after}'\n";
}
// string.trim: '  Walmir  ' → 'Walmir'
// string.upper_case: 'Walmir' → 'WALMIR'
```

## XSS Prevention

```php
$result = $engine->sanitize(
    ['input' => '<script>alert("xss")</script><b>Bold</b>'],
    ['input' => ['strip_tags', 'html_encode']],
);
// Result: "&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;Bold"
// Or with strip_tags alone: 'alert("xss")Bold'
```

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

## All 33 Rules

| Category | Rules | Aliases |
|----------|-------|---------|
| **String** (12) | Trim, LowerCase, UpperCase, Capitalize, Slug, Truncate, NormalizeWhitespace, NormalizeLineEndings, Pad, Replace, RegexReplace, StripNonPrintable | `trim`, `lower_case`, `upper_case`, `capitalize`, `slug`, `truncate`, `normalize_whitespace`, `normalize_line_endings`, `pad`, `replace`, `regex_replace`, `strip_non_printable` |
| **HTML** (5) | StripTags, HtmlEncode, HtmlDecode, HtmlPurify, UrlEncode | `strip_tags`, `html_encode`, `html_decode`, `html_purify`, `url_encode` |
| **Numeric** (4) | ToInt, ToFloat, Clamp, Round | `to_int`, `to_float`, `clamp`, `round` |
| **Type** (3) | ToBool, ToString, ToArray | `to_bool`, `to_string`, `to_array` |
| **Date** (2) | NormalizeDate, TimestampToDate | `normalize_date`, `timestamp_to_date` |
| **Filter** (4) | DigitsOnly, AlphaOnly, AlphanumericOnly, EmailFilter | `digits_only`, `alpha_only`, `alphanumeric_only`, `email_filter` |
| **Brazilian** (3) | FormatCPF, FormatCNPJ, FormatCEP | `format_cpf`, `format_cnpj`, `format_cep` |

## Engine API (Programmatic)

```php
$engine = (new SanitizerServiceProvider())->createEngine();

$result = $engine->sanitize(
    ['html' => '<b>test</b>', 'text' => '  spaces  '],
    ['html' => ['strip_tags', 'trim'], 'text' => ['trim', 'upper_case']],
);

$result->get('html');                // "test"
$result->get('text');                // "SPACES"
$result->wasModified();              // true
$result->modifiedFields();           // ['html', 'text']
$result->modificationCount();        // 4

foreach ($result->modificationsFor('html') as $mod) {
    echo "{$mod->ruleName}: '{$mod->before}' → '{$mod->after}'\n";
}
// html.strip_tags: '<b>test</b>' → 'test'
```

## Ecosystem Position

```
DPO Pipeline:     Validator → ★ Sanitizer ★ → Transformer → Business Logic
Infra Pipeline:   Object ↔ Normalizer ↔ Array ↔ Serializer ↔ String
Cross-Layer:      Request DTO ↔ Mapper ↔ Domain Entity ↔ Mapper ↔ Response DTO
```

The Sanitizer **cleans data** — removes noise while preserving semantic meaning. Contrast with the Transformer which converts representation (may change type). Key property: idempotency — `sanitize(sanitize(x)) = sanitize(x)`.

## Architecture

- ARFA 1.3 compliant (immutable context, reactive pipeline, observability events)
- Quality Directive V4.0 (all rules `final readonly`, zero dependencies)
- See [docs/](docs/) for 3 ADRs, 2 SPECs, and compliance report

## Metrics

| Metric | Value |
|--------|-------|
| Source files | 50 |
| Source lines | 1,913 |
| Test files | 15 |
| Test lines | 969 |
| Total | **65 files / 2,882 lines** |
| Rule classes | 33 |
| Rule categories | 7 |
| External dependencies | **0** |

## License

MIT © Walmir Silva — KaririCode Framework
