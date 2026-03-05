<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Html;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Decodes HTML entities back to characters.
 *
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 *
 * @since   3.1.0 ARFA 1.3
 */
final readonly class HtmlDecodeRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (! \is_string($value)) {
            return $value;
        }

        $flagsRaw = $context->getParameter('flags', ENT_QUOTES | ENT_SUBSTITUTE);
        $flags = \is_int($flagsRaw) ? $flagsRaw : ENT_QUOTES | ENT_SUBSTITUTE;
        $encodingRaw = $context->getParameter('encoding', 'UTF-8');
        $encoding = \is_string($encodingRaw) ? $encodingRaw : 'UTF-8';

        return html_entity_decode($value, $flags, $encoding);
    }

    #[\Override]
    public function getName(): string
    {
        return 'html.decode';
    }
}
