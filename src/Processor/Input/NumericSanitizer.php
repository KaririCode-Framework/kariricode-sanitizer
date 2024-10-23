<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Input;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;
use KaririCode\Sanitizer\Trait\CharacterFilterTrait;
use KaririCode\Sanitizer\Trait\NumericSanitizerTrait;
use KaririCode\Sanitizer\Trait\WhitespaceSanitizerTrait;

class NumericSanitizer extends AbstractSanitizerProcessor implements ConfigurableProcessor
{
    use NumericSanitizerTrait;
    use CharacterFilterTrait;
    use WhitespaceSanitizerTrait;

    private bool $allowDecimal = false;
    private bool $allowNegative = true;
    private string $decimalSeparator = '.';

    public function configure(array $options): void
    {
        $this->allowDecimal = $options['allowDecimal'] ?? $this->allowDecimal;
        $this->allowNegative = $options['allowNegative'] ?? $this->allowNegative;
        $this->decimalSeparator = $options['decimalSeparator'] ?? $this->decimalSeparator;
    }

    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);
        $input = $this->trimWhitespace($input);

        if ($this->shouldProcessOnlyIntegers()) {
            return $this->extractNumbers($input);
        }

        if ($this->shouldProcessOnlyPositiveDecimals()) {
            return $this->preserveDecimalPoint($input, $this->decimalSeparator);
        }

        return $this->processNumberWithSign($input);
    }

    private function shouldProcessOnlyIntegers(): bool
    {
        return !$this->allowDecimal && !$this->allowNegative;
    }

    private function shouldProcessOnlyPositiveDecimals(): bool
    {
        return $this->allowDecimal && !$this->allowNegative;
    }

    private function processNumberWithSign(string $input): string
    {
        $isNegative = $this->startsWithNegativeSign($input);
        $absoluteValue = $this->getAbsoluteValue($input);
        $processedValue = $this->processValue($absoluteValue);

        return $this->applySign($processedValue, $isNegative);
    }

    private function startsWithNegativeSign(string $input): bool
    {
        return str_starts_with(trim($input), '-');
    }

    private function getAbsoluteValue(string $input): string
    {
        return ltrim($input, '-');
    }

    private function processValue(string $input): string
    {
        if ($this->allowDecimal) {
            return $this->preserveDecimalPoint($input, $this->decimalSeparator);
        }

        return $this->extractNumbers($input);
    }

    private function applySign(string $value, bool $isNegative): string
    {
        if (!$isNegative || !$this->allowNegative) {
            return $value;
        }

        return '-' . $value;
    }
}
