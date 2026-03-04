<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Conformance;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Verifies all sanitization rule classes are final readonly
 * and implement SanitizationRule — ARFA 1.43 V4.0 conformance.
 */
#[CoversNothing]
final class ArchitecturalContractTest extends TestCase
{
    private const RULE_CLASSES = [
        // String (12)
        \KaririCode\Sanitizer\Rule\String\TrimRule::class,
        \KaririCode\Sanitizer\Rule\String\LowerCaseRule::class,
        \KaririCode\Sanitizer\Rule\String\UpperCaseRule::class,
        \KaririCode\Sanitizer\Rule\String\CapitalizeRule::class,
        \KaririCode\Sanitizer\Rule\String\SlugRule::class,
        \KaririCode\Sanitizer\Rule\String\TruncateRule::class,
        \KaririCode\Sanitizer\Rule\String\NormalizeWhitespaceRule::class,
        \KaririCode\Sanitizer\Rule\String\NormalizeLineEndingsRule::class,
        \KaririCode\Sanitizer\Rule\String\PadRule::class,
        \KaririCode\Sanitizer\Rule\String\ReplaceRule::class,
        \KaririCode\Sanitizer\Rule\String\RegexReplaceRule::class,
        \KaririCode\Sanitizer\Rule\String\StripNonPrintableRule::class,
        // Html (5)
        \KaririCode\Sanitizer\Rule\Html\StripTagsRule::class,
        \KaririCode\Sanitizer\Rule\Html\HtmlEncodeRule::class,
        \KaririCode\Sanitizer\Rule\Html\HtmlDecodeRule::class,
        \KaririCode\Sanitizer\Rule\Html\HtmlPurifyRule::class,
        \KaririCode\Sanitizer\Rule\Html\UrlEncodeRule::class,
        // Numeric (4)
        \KaririCode\Sanitizer\Rule\Numeric\ToIntRule::class,
        \KaririCode\Sanitizer\Rule\Numeric\ToFloatRule::class,
        \KaririCode\Sanitizer\Rule\Numeric\ClampRule::class,
        \KaririCode\Sanitizer\Rule\Numeric\RoundRule::class,
        // Type (3)
        \KaririCode\Sanitizer\Rule\Type\ToBoolRule::class,
        \KaririCode\Sanitizer\Rule\Type\ToStringRule::class,
        \KaririCode\Sanitizer\Rule\Type\ToArrayRule::class,
        // Date (2)
        \KaririCode\Sanitizer\Rule\Date\NormalizeDateRule::class,
        \KaririCode\Sanitizer\Rule\Date\TimestampToDateRule::class,
        // Filter (4)
        \KaririCode\Sanitizer\Rule\Filter\DigitsOnlyRule::class,
        \KaririCode\Sanitizer\Rule\Filter\AlphaOnlyRule::class,
        \KaririCode\Sanitizer\Rule\Filter\AlphanumericOnlyRule::class,
        \KaririCode\Sanitizer\Rule\Filter\EmailFilterRule::class,
        // Brazilian (3)
        \KaririCode\Sanitizer\Rule\Brazilian\FormatCpfRule::class,
        \KaririCode\Sanitizer\Rule\Brazilian\FormatCnpjRule::class,
        \KaririCode\Sanitizer\Rule\Brazilian\FormatCepRule::class,
    ];

    #[Test]
    public function testAllRulesAreFinalReadonly(): void
    {
        foreach (self::RULE_CLASSES as $class) {
            $ref = new \ReflectionClass($class);
            $this->assertTrue($ref->isFinal(), "{$class} must be final");
            $this->assertTrue($ref->isReadOnly(), "{$class} must be readonly");
        }
    }

    #[Test]
    public function testAllRulesImplementContract(): void
    {
        foreach (self::RULE_CLASSES as $class) {
            $this->assertTrue(
                is_subclass_of($class, \KaririCode\Sanitizer\Contract\SanitizationRule::class),
                "{$class} must implement SanitizationRule",
            );
        }
    }

    #[Test]
    public function testRuleCount(): void
    {
        $this->assertCount(33, self::RULE_CLASSES);
    }
}
