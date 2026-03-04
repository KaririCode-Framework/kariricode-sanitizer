# SPEC-003: Attribute Sanitizer (`#[Sanitize]`)

**Version:** 3.2.0 | **ARFA:** 1.43 V4.0

## 1. Attribute Definition

```php
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
final readonly class Sanitize
{
    /** @var list<string|array{0: string, 1: array<string, mixed>}> */
    public array $rules;

    public function __construct(string|array ...$rules) {}
}
```

Multiple `#[Sanitize]` on the same property are **merged**, so:

```php
#[Sanitize('trim')]
#[Sanitize('capitalize')]
public string $name;

// is equivalent to:
#[Sanitize('trim', 'capitalize')]
public string $name;
```

---

## 2. Rule Definition Formats

```php
// Alias only
#[Sanitize('trim', 'capitalize')]
public string $name;

// Alias with parameters
#[Sanitize(['truncate', ['max' => 200, 'suffix' => '…']])]
public string $bio;

// Mixed
#[Sanitize('trim', ['truncate', ['max' => 50]])]
public string $title;
```

---

## 3. Attribute Sanitizer Flow

```
1. SanitizerServiceProvider::createAttributeSanitizer()
   → AttributeSanitizer(engine, inspector)

2. AttributeSanitizer::sanitize(object $dto)
   a. PropertyInspector::inspect($dto, SanitizeAttributeHandler)
      → SanitizeAttributeHandler::handleAttribute(property, Sanitize, value)
         accumulates: fieldRules[property] += Sanitize::$rules
                      data[property] = current property value
   b. engine::sanitize(data, fieldRules) → SanitizationResult
   c. SanitizeAttributeHandler::setProcessedValues(result->getSanitizedData())
   d. SanitizeAttributeHandler::applyChanges($dto)
      → sets each property value via ReflectionProperty::setValue()

3. Return SanitizationResult
```

---

## 4. `SanitizerServiceProvider` Bootstrap

```php
$sanitizer = (new SanitizerServiceProvider())->createAttributeSanitizer();

$dto = new UserDto(name: '  ALICE  ', email: 'ALICE@EXAMPLE.COM  ');
$result = $sanitizer->sanitize($dto);

echo $dto->name;   // 'Alice'   (trim + capitalize applied to object in-place)
echo $dto->email;  // 'alice@example.com'
```

---

## 5. Events

The engine dispatches two value objects accessible from `SanitizationResult`:

| Event | Trigger | Data |
|-------|---------|------|
| `SanitizationStartedEvent` | Before rule chain starts | input data, rule map, timestamp |
| `SanitizationCompletedEvent` | After all fields processed | sanitized data, result, duration |

---

## 6. Exceptions

| Exception | Factory Method | Trigger |
|-----------|---------------|---------|
| `SanitizationException` | `::ruleNotFound(alias)` | Alias not in registry |
| `SanitizationException` | `::processingFailed(field, reason)` | Rule throws at runtime |
| `InvalidRuleException` | `::invalidRule(alias, reason)` | Invalid rule configuration |
| `InvalidRuleException` | `::unsupportedType(alias, type)` | Unexpected type passed to rule |
