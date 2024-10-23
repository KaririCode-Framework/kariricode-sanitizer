<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Input;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;
use KaririCode\Sanitizer\Trait\NumericSanitizerTrait;

class PhoneSanitizer extends AbstractSanitizerProcessor implements ConfigurableProcessor
{
    use NumericSanitizerTrait;

    private bool $applyFormat = false;
    private string $format = '';
    private string $placeholder = '#';

    public function configure(array $options): void
    {
        if (isset($options['applyFormat'])) {
            $this->applyFormat = (bool) $options['applyFormat'];
            $this->format = $options['format'] ?? '(##) #####-####';
            $this->placeholder = $options['placeholder'] ?? '#';
        }
    }

    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);
        $numbers = $this->extractNumbers($input);

        if (!$this->shouldApplyFormat($numbers)) {
            return $numbers;
        }

        return $this->formatPhoneNumber($numbers);
    }

    private function shouldApplyFormat(string $numbers): bool
    {
        if (!$this->applyFormat || empty($this->format)) {
            return false;
        }

        $requiredLength = substr_count($this->format, $this->placeholder);

        return strlen($numbers) === $requiredLength;
    }

    private function formatPhoneNumber(string $numbers): string
    {
        $result = $this->format;
        $position = 0;

        for ($i = 0; $i < strlen($result); ++$i) {
            if ($result[$i] === $this->placeholder) {
                if (isset($numbers[$position])) {
                    $result[$i] = $numbers[$position++];
                }
            }
        }

        return $result;
    }
}
