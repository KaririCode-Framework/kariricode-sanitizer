<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Provider;

use KaririCode\Sanitizer\Configuration\SanitizerConfiguration;
use KaririCode\Sanitizer\Core\AttributeSanitizer;
use KaririCode\Sanitizer\Core\InMemoryRuleRegistry;
use KaririCode\Sanitizer\Core\SanitizerEngine;
use KaririCode\Sanitizer\Rule\Brazilian;
use KaririCode\Sanitizer\Rule\Date;
use KaririCode\Sanitizer\Rule\Filter;
use KaririCode\Sanitizer\Rule\Html;
use KaririCode\Sanitizer\Rule\Numeric;
use KaririCode\Sanitizer\Rule\String\CapitalizeRule;
use KaririCode\Sanitizer\Rule\String\LowerCaseRule;
use KaririCode\Sanitizer\Rule\String\NormalizeLineEndingsRule;
use KaririCode\Sanitizer\Rule\String\NormalizeWhitespaceRule;
use KaririCode\Sanitizer\Rule\String\PadRule;
use KaririCode\Sanitizer\Rule\String\RegexReplaceRule;
use KaririCode\Sanitizer\Rule\String\ReplaceRule;
use KaririCode\Sanitizer\Rule\String\SlugRule;
use KaririCode\Sanitizer\Rule\String\StripNonPrintableRule;
use KaririCode\Sanitizer\Rule\String\TrimRule;
use KaririCode\Sanitizer\Rule\String\TruncateRule;
use KaririCode\Sanitizer\Rule\String\UpperCaseRule;
use KaririCode\Sanitizer\Rule\Type;

/**
 * Registers all 33 built-in sanitization rules.
 *
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 *
 * @since   3.1.0 ARFA 1.3
 */
final class SanitizerServiceProvider
{
    public function createRegistry(): InMemoryRuleRegistry
    {
        $registry = new InMemoryRuleRegistry();
        $this->registerBuiltinRules($registry);

        return $registry;
    }

    public function createEngine(?SanitizerConfiguration $configuration = null): SanitizerEngine
    {
        return new SanitizerEngine($this->createRegistry(), $configuration);
    }

    public function createAttributeSanitizer(?SanitizerConfiguration $configuration = null): AttributeSanitizer
    {
        return new AttributeSanitizer($this->createEngine($configuration));
    }

    private function registerBuiltinRules(InMemoryRuleRegistry $registry): void
    {
        // ── String (12) ───────────────────────────────────────────
        $registry->register('trim', new TrimRule());
        $registry->register('lower_case', new LowerCaseRule());
        $registry->register('upper_case', new UpperCaseRule());
        $registry->register('capitalize', new CapitalizeRule());
        $registry->register('slug', new SlugRule());
        $registry->register('truncate', new TruncateRule());
        $registry->register('normalize_whitespace', new NormalizeWhitespaceRule());
        $registry->register('normalize_line_endings', new NormalizeLineEndingsRule());
        $registry->register('pad', new PadRule());
        $registry->register('replace', new ReplaceRule());
        $registry->register('regex_replace', new RegexReplaceRule());
        $registry->register('strip_non_printable', new StripNonPrintableRule());

        // ── Html (5) ──────────────────────────────────────────────
        $registry->register('strip_tags', new Html\StripTagsRule());
        $registry->register('html_encode', new Html\HtmlEncodeRule());
        $registry->register('html_decode', new Html\HtmlDecodeRule());
        $registry->register('html_purify', new Html\HtmlPurifyRule());
        $registry->register('url_encode', new Html\UrlEncodeRule());

        // ── Numeric (4) ───────────────────────────────────────────
        $registry->register('to_int', new Numeric\ToIntRule());
        $registry->register('to_float', new Numeric\ToFloatRule());
        $registry->register('clamp', new Numeric\ClampRule());
        $registry->register('round', new Numeric\RoundRule());

        // ── Type (3) ──────────────────────────────────────────────
        $registry->register('to_bool', new Type\ToBoolRule());
        $registry->register('to_string', new Type\ToStringRule());
        $registry->register('to_array', new Type\ToArrayRule());

        // ── Date (2) ──────────────────────────────────────────────
        $registry->register('normalize_date', new Date\NormalizeDateRule());
        $registry->register('timestamp_to_date', new Date\TimestampToDateRule());

        // ── Filter (4) ────────────────────────────────────────────
        $registry->register('digits_only', new Filter\DigitsOnlyRule());
        $registry->register('alpha_only', new Filter\AlphaOnlyRule());
        $registry->register('alphanumeric_only', new Filter\AlphanumericOnlyRule());
        $registry->register('email_filter', new Filter\EmailFilterRule());

        // ── Brazilian (3) ─────────────────────────────────────────
        $registry->register('format_cpf', new Brazilian\FormatCpfRule());
        $registry->register('format_cnpj', new Brazilian\FormatCnpjRule());
        $registry->register('format_cep', new Brazilian\FormatCepRule());
    }
}
