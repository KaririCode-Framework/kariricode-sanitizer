# KaririCode Sanitizer

<div align="center">

[![CI](https://github.com/KaririCode-Framework/kariricode-sanitizer/actions/workflows/ci.yml/badge.svg)](https://github.com/KaririCode-Framework/kariricode-sanitizer/actions/workflows/ci.yml)
[![PHP 8.4+](https://img.shields.io/badge/PHP-8.4%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-22c55e.svg)](LICENSE)
[![PHPStan Level 9](https://img.shields.io/badge/PHPStan-Level%209-4F46E5)](https://phpstan.org/)
[![Psalm](https://img.shields.io/badge/Psalm-Level%201-4F46E5)](https://psalm.dev/)
[![Tests](https://img.shields.io/badge/Tests-175%20passing-22c55e)](tests/)
[![Coverage](https://img.shields.io/badge/Coverage-100%25-22c55e)](tests/)
[![Rules](https://img.shields.io/badge/Rules-33-22c55e)](docs/spec/SPEC-002-rule-reference.md)
[![ARFA](https://img.shields.io/badge/ARFA-1.3-F97316)](https://kariricode.org)
[![KaririCode Framework](https://img.shields.io/badge/KaririCode-Framework-F97316)](https://kariricode.org)

**Composable, rule-based data sanitization engine for PHP 8.4+.**  
33 built-in rules · XSS prevention · #[Sanitize] attributes · zero dependencies.

[Installation](#installation) · [Quick Start](#quick-start) · [Attribute API](#attribute-driven-dto-sanitization) · [All 33 Rules](#all-33-rules) · [CI Integration](#ci-integration) · [Architecture](#architecture)

</div>

---

## The Problem

User input arrives as raw strings — untrusted, inconsistent, and potentially malicious:

```php
// Raw input from request — dangers everywhere
$email    = $_POST['email'];   // "  ALICE@EXAMPLE.COM  " — extra spaces, wrong case
$bio      = $_POST['bio'];     // "<script>alert('XSS')</script>Hello!"
$phone    = $_POST['phone'];   // "(11) 9 9999.9999" — inconsistent format
$username = $_POST['username']; // "Alice O'Connor; DROP TABLE users; --"
```

## The Solution

```php
use KaririCode\Sanitizer\Provider\SanitizerServiceProvider;

$engine = (new SanitizerServiceProvider())->createEngine();

$result = $engine->sanitize(
    data: ['email' => '  ALICE@EXAMPLE.COM  ', 'bio' => '<script>alert(1)</script>Hello!'],
    rules: [
        'email' => ['trim', 'lowercase'],
        'bio'   => ['html.encode', ['truncate', ['max' => 200, 'suffix' => '…']]],
    ],
);

// $result->get('email') === "alice@example.com"
// $result->get('bio')   === "&lt;script&gt;alert(1)&lt;/script&gt;Hello!"
```

---

## Features

- ✅ **33 built-in rules** across 7 categories (String, HTML, Numeric, Date, Filter, Type, Brazilian)
- ✅ **XSS prevention** — `html.encode`, `strip_tags`, `remove_scripts` built-in
- ✅ **Attribute-driven API** — annotate DTOs with `#[Sanitize]`, zero boilerplate
- ✅ **Dot-notation paths** — sanitize nested fields like `address.city`
- ✅ **Zero external dependencies** — only `kariricode/property-inspector` for reflection
- ✅ **PHPStan Level 9 + Psalm Level 1** — full static-analysis compliance
- ✅ **100% test coverage** — 175 tests, 100% Classes/Methods/Lines
- ✅ **ARFA 1.3 compliant** — `final readonly` domain classes, immutable context

---

## Requirements

| Requirement | Version |
|-------------|---------|
| PHP | 8.4+ |
| Composer | 2.x |
| kariricode/property-inspector | ^2.0 |

---

## Installation

```bash
composer require kariricode/sanitizer
```

---

## Quick Start

### 1. Engine API

```php
use KaririCode\Sanitizer\Provider\SanitizerServiceProvider;

$engine = (new SanitizerServiceProvider())->createEngine();

$result = $engine->sanitize(
    data:  ['name' => '  WALMIR SILVA  ', 'bio' => '<b>Developer</b> & creator'],
    rules: ['name' => ['trim', 'capitalize'], 'bio' => ['html.decode', 'trim']],
);

echo $result->get('name'); // "Walmir Silva"
echo $result->get('bio');  // "Developer & creator"
```

### 2. Attribute API (DTO-driven)

```php
use KaririCode\Sanitizer\Attribute\Sanitize;
use KaririCode\Sanitizer\Provider\SanitizerServiceProvider;

final class UserRegistrationInput
{
    public function __construct(
        #[Sanitize('trim', 'lowercase')]
        public string $email,

        #[Sanitize('trim', 'html.encode')]
        public string $bio,

        #[Sanitize('trim', 'remove_scripts')]
        public string $username,
    ) {}
}

$provider  = new SanitizerServiceProvider();
$sanitizer = $provider->createAttributeSanitizer($provider->createConfiguration());

$input  = new UserRegistrationInput('  ALICE@EXAMPLE.COM  ', '<b>Hi!</b>', '<script>evil</script>Alice');
$result = $sanitizer->sanitize($input);
// ['email' => 'alice@example.com', 'bio' => '&lt;b&gt;Hi!&lt;/b&gt;', 'username' => 'Alice']
```

---

## Real-World Use Cases

### Use Case 1 — User Registration Form

```php
final class RegistrationInput
{
    #[Sanitize('trim', 'lowercase')]
    public string $email;

    #[Sanitize('trim', ['min_length' => ['min' => 8]], 'capitalize')]
    public string $name;

    #[Sanitize('trim', 'html.encode', ['truncate', ['max' => 500]])]
    public string $bio;

    #[Sanitize('remove_non_numeric')]  // strips dashes, spaces, brackets
    public string $phone;
}
```

### Use Case 2 — API Payload Normalisation (Nested Fields)

```php
$engine->sanitize(
    data: ['user' => ['name' => '  Alice  ', 'email' => 'ALICE@EXAMPLE.COM']],
    rules: [
        'user.name'  => ['trim', 'capitalize'],
        'user.email' => ['trim', 'lowercase'],
    ],
);
```

### Use Case 3 — CMS Rich-Text Sanitization

```php
$engine->sanitize(
    data: ['content' => $htmlFromEditor],
    rules: [
        'content' => [
            ['allowed_tags', ['tags' => '<p><br><strong><em><ul><ol><li>']],
            'remove_scripts',
            'normalize_whitespace',
        ],
    ],
);
```

### Use Case 4 — Brazilian Document Formatting

```php
$engine->sanitize(
    data:  ['cpf' => '123.456.789-01', 'cnpj' => '12.345.678/0001-99'],
    rules: ['cpf' => ['remove_non_numeric'], 'cnpj' => ['remove_non_numeric']],
);
// Strips formatting → pure digits for database storage
```

---

## All 33 Rules

### String
| Alias | Description |
|-------|-------------|
| `trim` | Remove surrounding whitespace |
| `lowercase` / `uppercase` | Case normalisation |
| `capitalize` | Capitalise first letter of each word |
| `slug` | Convert to URL-safe slug |
| `remove_whitespace` | Remove all whitespace |
| `normalize_whitespace` | Collapse multiple spaces |
| `remove_special_chars` | Strip non-alphanumeric |
| `remove_non_numeric` | Digits only |
| `remove_non_alpha` | Letters only |
| `truncate` | Truncate with suffix · `max`, `suffix` |
| `pad` | Pad string · `length`, `pad_char`, `side` |
| `repeat` | Repeat string · `times` |
| `reverse` | Reverse characters |
| `mask` | Mask characters · `start`, `end`, `char` |

### HTML
| Alias | Description |
|-------|-------------|
| `html.encode` | Encode HTML entities |
| `html.decode` | Decode HTML entities |
| `strip_tags` | Remove HTML/PHP tags |
| `allowed_tags` | Remove all tags except allowed · `tags: string` |
| `remove_scripts` | Remove `<script>` blocks |

### Numeric
| Alias | Parameters | Description |
|-------|-----------|-------------|
| `round` | `precision: int` | Round to decimal places |
| `abs` | — | Absolute value |
| `clamp` | `min`, `max` | Clamp to range |
| `number_format` | `decimals`, `dec_sep`, `thou_sep` | Format number |

### Date
| Alias | Parameters | Description |
|-------|-----------|-------------|
| `date.format` | `from`, `to` | Re-format date string |
| `date.to_iso` | — | Any date → ISO 8601 |

### Filter
| Alias | Description |
|-------|-------------|
| `filter.email` | Remove invalid email characters |
| `filter.url` | Safe URL characters only |
| `filter.filename` | Remove path-traversal characters |

### Type
| Alias | Description |
|-------|-------------|
| `to_int` | Cast to integer |
| `to_float` | Cast to float |
| `to_bool` | Cast to boolean |
| `to_string` | Cast to string |

### Brazilian
| Alias | Description |
|-------|-------------|
| `br.cpf` | Strip formatting from CPF |
| `br.cnpj` | Strip formatting from CNPJ |
| `br.cep` | Strip formatting from CEP |

---

## CI Integration

```yaml
# .github/workflows/ci.yml (excerpt)
- name: Run quality pipeline
  run: |
    kcode init
    sed -i 's/beStrictAboutCoverageMetadata="true"/beStrictAboutCoverageMetadata="false"/' \
        .kcode/phpunit.xml.dist
    kcode quality
```

| Tool | Level | Result |
|------|-------|--------|
| `php-cs-fixer` | KaririCode standard | ✅ |
| `phpstan` | Level 9 | ✅ |
| `psalm` | Level 1 | ✅ |
| `phpunit` + `pcov` | 100% coverage | ✅ |

---

## Architecture

### Design Decisions

| Decision | Rationale | ADR |
|----------|-----------|-----|
| Alias-based in-memory registry | Decouples engine from rules; custom rules register via alias | [ADR-001](docs/adr/ADR-001-rule-registry-pattern.md) |
| `PropertyInspector` for `#[Sanitize]` | Reflection caching; zero manual ReflectionClass loops | [ADR-002](docs/adr/ADR-002-property-inspector-integration.md) |
| `SanitizationContext` immutability | `final readonly` + builder pattern; prevents cross-field pollution | [ADR-003](docs/adr/ADR-003-sanitization-context-immutability.md) |
| `final readonly` on all rules | ARFA Principle 11 — immutable data path | [SPEC-001](docs/spec/SPEC-001-sanitizer-engine.md) |

### Project Stats

| Metric | Value |
|--------|-------|
| Source files | 40+ |
| Rule classes | 33 |
| External dependencies | 1 (`kariricode/property-inspector`) |
| PHPUnit tests | 175 |
| Line coverage | 100% |
| PHPStan level | 9 (0 errors) |
| Psalm level | 1 (0 errors) |

---

## Ecosystem Integration

| Library | Integration |
|---------|-------------|
| [kariricode/validator](https://github.com/KaririCode-Framework/kariricode-validator) | Sanitize input before validation in DPO pipeline |
| [kariricode/transformer](https://github.com/KaririCode-Framework/kariricode-transformer) | Sanitize → Transform → Persist pipeline |
| [kariricode/normalizer](https://github.com/KaririCode-Framework/kariricode-normalizer) | Sanitize, then normalize to portable format |
| [kariricode/property-inspector](https://github.com/KaririCode-Framework/kariricode-property-inspector) | Reflection caching for `#[Sanitize]` attribute scanning |
| [kariricode/devkit](https://github.com/KaririCode-Framework/kariricode-devkit) | `kcode quality` CI pipeline, PHPStan/Psalm/cs-fixer unified runner |

---

## Contributing

1. Fork the repository  
2. Create a feature branch: `git checkout -b feat/my-sanitizer-rule`  
3. Run: `kcode quality` — all 4 tools must pass  
4. Submit a pull request against `develop`

---

<div align="center">

**Part of the [KaririCode Framework](https://kariricode.org) ecosystem.**

[![GitHub](https://img.shields.io/badge/GitHub-KaririCode-181717?logo=github)](https://github.com/KaririCode-Framework)
[![Packagist](https://img.shields.io/badge/Packagist-kariricode%2Fsanitizer-F28D1A?logo=packagist&logoColor=white)](https://packagist.org/packages/kariricode/sanitizer)

*Built with ❤️ by [Walmir Silva](https://github.com/walmir-silva) · [kariricode.org](https://kariricode.org)*

</div>
