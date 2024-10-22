<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Input;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;
use KaririCode\Sanitizer\Trait\CaseTransformerTrait;
use KaririCode\Sanitizer\Trait\CharacterFilterTrait;
use KaririCode\Sanitizer\Trait\CharacterReplacementTrait;
use KaririCode\Sanitizer\Trait\WhitespaceSanitizerTrait;

class EmailSanitizer extends AbstractSanitizerProcessor implements ConfigurableProcessor
{
    use WhitespaceSanitizerTrait;
    use CaseTransformerTrait;
    use CharacterReplacementTrait;
    use CharacterFilterTrait;

    private const COMMON_TYPOS = [
        ',' => '.',
        ';' => '.',
        'mailto:' => '',
    ];
    private const DOMAIN_REPLACEMENTS = [
        'gmail.com' => ['gmial.com', 'gmai.com', 'gmaill.com', 'gamil.com', 'gmail.comm'],
        'yahoo.com' => ['yaho.com', 'yahooo.com', 'yahoo.comm'],
        'hotmail.com' => ['hotmal.com', 'hotmail.comm', 'hotmal.com'],
        'outlook.com' => ['outlok.com', 'outlook.comm', 'outlock.com'],
    ];

    private array $typoReplacements;
    private array $domainReplacements;
    private bool $removeMailtoPrefix = true;

    public function __construct()
    {
        $this->typoReplacements = self::COMMON_TYPOS;
        $this->domainReplacements = self::DOMAIN_REPLACEMENTS;
    }

    public function configure(array $options): void
    {
        $this->configureReplacements($options);
        $this->configureBehavior($options);
    }

    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);

        return $this->buildSanitizedEmail($input);
    }

    private function buildSanitizedEmail(string $input): string
    {
        $email = $this->performBasicSanitization($input);

        if ($this->containsAtSymbol($email)) {
            $email = $this->processEmailParts($email);
        }

        return $email;
    }

    private function performBasicSanitization(string $input): string
    {
        $email = $this->trimWhitespace($input);
        $email = $this->toLowerCase($email);

        if ($this->removeMailtoPrefix) {
            $email = $this->removeMailtoPrefix($email);
        }

        $email = $this->replaceMultipleCharacters($email, $this->typoReplacements);

        return $this->replaceConsecutiveCharacters($email, '.', '.');
    }

    private function containsAtSymbol(string $email): bool
    {
        return str_contains($email, '@');
    }

    private function processEmailParts(string $email): string
    {
        [$localPart, $domain] = explode('@', $email, 2);
        $domain = $this->fixDomainTypos($domain);

        return $localPart . '@' . $domain;
    }

    private function removeMailtoPrefix(string $email): string
    {
        return str_replace('mailto:', '', $email);
    }

    private function fixDomainTypos(string $domain): string
    {
        foreach ($this->domainReplacements as $correct => $typos) {
            if ($this->isDomainTypo($domain, $typos)) {
                return $correct;
            }
        }

        return $domain;
    }

    private function isDomainTypo(string $domain, array $typos): bool
    {
        return in_array($domain, $typos, true);
    }

    private function configureReplacements(array $options): void
    {
        if (isset($options['typoReplacements'])) {
            $this->typoReplacements = array_merge(
                $this->typoReplacements,
                $options['typoReplacements']
            );
        }

        if (isset($options['domainReplacements'])) {
            $this->domainReplacements = array_merge(
                $this->domainReplacements,
                $options['domainReplacements']
            );
        }
    }

    private function configureBehavior(array $options): void
    {
        $this->removeMailtoPrefix = $options['removeMailtoPrefix']
            ?? $this->removeMailtoPrefix;
    }
}
