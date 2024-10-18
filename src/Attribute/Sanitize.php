<?php

namespace KaririCode\Sanitizer\Attribute;

use KaririCode\Contract\Processor\Attribute\CustomizableMessageAttribute;
use KaririCode\Contract\Processor\Attribute\ProcessableAttribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Sanitize implements ProcessableAttribute, CustomizableMessageAttribute
{
    private readonly array $processors;
    private readonly array $messages;

    public function __construct(array $processors, ?array $messages = null)
    {
        $this->processors = array_filter($processors, fn ($v) => !is_null($v) && false !== $v);
        $this->messages = $messages ?? [];
    }

    public function getProcessors(): array
    {
        return $this->processors;
    }

    public function getMessage($processorName): ?string
    {
        return $this->messages[$processorName] ?? null;
    }
}
