# SPEC-002: Rule Reference Catalog

**Version:** 3.2.0 | **ARFA:** 1.43 V4.0

All rules implement `SanitizationRule`. Non-matching input types are returned unchanged (ADR-004).

---

## String Rules (`src/Rule/String/`)

| Alias | Class | Parameters | Default |
|-------|-------|-----------|---------|
| `string.trim` | `TrimRule` | `characters` (string) | `" \t\n\r\0\x0B"` |
| `string.lower_case` | `LowerCaseRule` | — | — |
| `string.upper_case` | `UpperCaseRule` | — | — |
| `string.capitalize` | `CapitalizeRule` | — | — |
| `string.slug` | `SlugRule` | `separator` (string) | `'-'` |
| `string.truncate` | `TruncateRule` | `max` (int), `suffix` (string) | `255`, `'...'` |
| `string.pad` | `PadRule` | `length` (int), `pad` (string), `side` (string) | `0`, `' '`, `'right'` |
| `string.replace` | `ReplaceRule` | `search` (string), `replace` (string) | `''`, `''` |
| `string.regex_replace` | `RegexReplaceRule` | `pattern` (string), `replacement` (string) | `''`, `''` |
| `string.normalize_whitespace` | `NormalizeWhitespaceRule` | — | — |
| `string.normalize_line_endings` | `NormalizeLineEndingsRule` | — | — |
| `string.strip_non_printable` | `StripNonPrintableRule` | — | — |

**`PadRule` sides:** `'left'` → `STR_PAD_LEFT`, `'right'` → `STR_PAD_RIGHT`, `'both'` → `STR_PAD_BOTH`

---

## HTML Rules (`src/Rule/Html/`)

| Alias | Class | Parameters | Default |
|-------|-------|-----------|---------|
| `html.strip_tags` | `StripTagsRule` | `allowed` (string) | `''` |
| `html.encode` | `HtmlEncodeRule` | `flags` (int), `encoding` (string), `double_encode` (bool) | `ENT_QUOTES\|ENT_SUBSTITUTE`, `'UTF-8'`, `true` |
| `html.decode` | `HtmlDecodeRule` | `flags` (int), `encoding` (string) | `ENT_QUOTES\|ENT_SUBSTITUTE`, `'UTF-8'` |
| `html.purify` | `HtmlPurifyRule` | `allowed` (string) | `''` |
| `html.url_encode` | `UrlEncodeRule` | `raw` (bool) | `false` |

**`HtmlPurifyRule` pipeline:** `strip_tags → html_entity_decode → trim`
**`UrlEncodeRule` raw=true:** uses `rawurlencode()` (RFC 3986); raw=false uses `urlencode()`.

---

## Filter Rules (`src/Rule/Filter/`)

| Alias | Class | Description |
|-------|-------|-------------|
| `filter.alpha_only` | `AlphaOnlyRule` | Keep only `[a-zA-Z]` characters |
| `filter.alphanumeric_only` | `AlphanumericOnlyRule` | Keep only `[a-zA-Z0-9]` characters |
| `filter.digits_only` | `DigitsOnlyRule` | Keep only `[0-9]` characters |
| `filter.email` | `EmailFilterRule` | Lowercase, trim, remove illegal email chars |

---

## Numeric Rules (`src/Rule/Numeric/`)

| Alias | Class | Parameters | Default |
|-------|-------|-----------|---------|
| `numeric.round` | `RoundRule` | `precision` (int), `mode` (string) | `2`, `'round'` |
| `numeric.clamp` | `ClampRule` | `min` (int\|float), `max` (int\|float) | `null`, `null` |
| `numeric.to_int` | `ToIntRule` | — | — |
| `numeric.to_float` | `ToFloatRule` | — | — |

**`RoundRule` modes:** `'round'` → `round()`, `'ceil'` → `ceil()`, `'floor'` → `floor()`

---

## Date Rules (`src/Rule/Date/`)

| Alias | Class | Parameters | Default |
|-------|-------|-----------|---------|
| `date.normalize` | `NormalizeDateRule` | `from` (string), `to` (string) | `'d/m/Y'`, `'Y-m-d'` |
| `date.timestamp_to_date` | `TimestampToDateRule` | `format` (string), `timezone` (string) | `'Y-m-d H:i:s'`, `'UTC'` |

---

## Type Rules (`src/Rule/Type/`)

| Alias | Class | Description |
|-------|-------|-------------|
| `type.to_bool` | `ToBoolRule` | `'true'/'yes'/'1'` → `true`; `'false'/'no'/'0'` → `false`; numeric → `(bool)` |
| `type.to_string` | `ToStringRule` | Cast scalar/stringable to string |
| `type.to_array` | `ToArrayRule` | Wrap non-array value in `[$value]` |

---

## Brazilian Rules (`src/Rule/Brazilian/`)

| Alias | Class | Description |
|-------|-------|-------------|
| `brazilian.format_cpf` | `FormatCpfRule` | Mask 11-digit CPF as `000.000.000-00` |
| `brazilian.format_cnpj` | `FormatCnpjRule` | Mask 14-digit CNPJ as `00.000.000/0000-00` |
| `brazilian.format_cep` | `FormatCepRule` | Mask 8-digit CEP as `00000-000` |
