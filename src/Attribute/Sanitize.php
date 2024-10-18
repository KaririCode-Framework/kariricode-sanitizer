<?php

namespace KaririCode\Sanitizer\Attribute;

use KaririCode\Contract\Processor\Attribute\BaseProcessorAttribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Sanitize extends BaseProcessorAttribute
{
}
