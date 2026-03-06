# SPEC-001: Sanitizer Engine

**Version:** 3.2.0 | **ARFA:** 1.43 V4.0

## 1. Engine Contract

```php
final class SanitizerEngine
{
    public function __construct(
        private RuleRegistry          $registry,
        private SanitizerConfiguration $config,
    ) {}

    /**
     * @param array<string, mixed>                                                          $data
     * @param array<string, list<SanitizationRule|string|array{SanitizationRule|string, array<string, mixed>}>> $rules
     */
    public function sanitize(array $data, array $rules): SanitizationResult;
    public function getConfiguration(): SanitizerConfiguration;
}
```

## 2. Sanitize Flow

```
For each field defined in $rules:
  1. resolveValue($data, $field)         — supports dot-notation ("user.email")
  2. For each rule definition in list:
     a. resolveRule($definition)         — alias string → (SanitizationRule, params[])
     b. Build SanitizationContextImpl(params)
     c. $rule->sanitize($value, $context) → $value   (chained)
  3. setSanitizedValue($result, $field, $value)
Return SanitizationResult
```

## 3. Rule Definition Formats

All three formats are equivalent at the call site:

```php
// 1. Alias string (registered in RuleRegistry)
'name' => ['trim', 'capitalize']

// 2. Alias string with parameters
'bio'  => [['truncate', ['max' => 200, 'suffix' => '…']]]

// 3. Direct SanitizationRule instance (bypasses registry)
'tag'  => [new SlugRule()]

// 4. Direct instance with parameters
'tag'  => [[new SlugRule(), ['separator' => '_']]]
```

## 4. Dot-Notation Field Access

`resolveValue($data, 'address.city')` traverses nested arrays using `explode('.', $field)`.
If any key in the path is absent, the engine returns `null` and skips the rule chain
for that field — no exception is thrown.

## 5. Result Shape

```php
$result->get('field')           // sanitized value for a field
$result->getSanitizedData()     // array<string, mixed> — all sanitized fields
$result->wasModified('field')   // bool — whether sanitization changed the value
$result->getModifiedFields()    // list<string>
$result->count()                // int — number of fields processed
```

## 6. Configuration

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `trackModifications` | `bool` | `true` | Record which fields changed |
| `preserveOriginal` | `bool` | `false` | Keep original values in result |

## 7. Error Handling

`SanitizationException::ruleNotFound(alias)` is thrown when an alias string cannot be
resolved in the registry. Direct `SanitizationRule` instances never throw on resolution.
`InvalidRuleException::invalidRule(alias, reason)` covers rule misconfiguration.

## Quality Gate (kcode DevKit)

This component uses `kcode` (KaririCode DevKit global binary) for all quality checks.
See [ARFA Spec V4.0 §16](https://github.com/KaririCode-Framework/kariricode-devkit) for the full infrastructure standard.

### Bootstrap (once per machine/CI)

```bash
# Install kcode globally
curl -L https://github.com/KaririCode-Framework/kariricode-devkit/releases/latest/download/kcode.phar \
     -o /usr/local/bin/kcode && chmod +x /usr/local/bin/kcode

# Bootstrap project toolchain
kcode init
```

### Common Commands

| Command | Description |
|---------|-------------|
| `kcode quality` | Full pipeline: cs-fix → analyse → test |
| `kcode test` | PHPUnit + pcov coverage |
| `kcode analyse` | PHPStan L9 + Psalm L1 |
| `kcode cs:fix` | Apply KaririCode code style |
| `kcode cs:fix --check` | Dry-run style check |
| `kcode security` | `composer audit` vulnerability scan |

### Internal Tool Direct Access

After `kcode init`, tools are under `.kcode/vendor/bin/`:

```bash
# PHPStan with custom flags
.kcode/vendor/bin/phpstan analyse --level=9 --configuration=.kcode/phpstan.neon src/

# Psalm — auto-add #[Override]
.kcode/vendor/bin/psalm --config=.kcode/psalm.xml --alter --issues=MissingOverrideAttribute

# php-cs-fixer — preview diff
.kcode/vendor/bin/php-cs-fixer fix --config=.kcode/php-cs-fixer.php --dry-run --diff

# PHPUnit — single test filter
.kcode/vendor/bin/phpunit --configuration=.kcode/phpunit.xml.dist --filter=testMyMethod
```
