<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Domain;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;
use KaririCode\Sanitizer\Processor\Domain\HtmlPurifier\Cleaner\InputCleaner;
use KaririCode\Sanitizer\Processor\Domain\HtmlPurifier\Cleaner\OutputCleaner;
use KaririCode\Sanitizer\Processor\Domain\HtmlPurifier\Configuration;
use KaririCode\Sanitizer\Processor\Domain\HtmlPurifier\DomHandler;
use KaririCode\Sanitizer\Processor\Domain\HtmlPurifier\NodeSanitizer;

final class HtmlPurifierSanitizer extends AbstractSanitizerProcessor implements ConfigurableProcessor
{
    private Configuration $config;
    private DomHandler $domHandler;
    private NodeSanitizer $nodeSanitizer;
    private InputCleaner $inputCleaner;
    private OutputCleaner $outputCleaner;

    public function __construct()
    {
        $this->config = new Configuration();
        $this->domHandler = new DomHandler();
        $this->nodeSanitizer = new NodeSanitizer($this->config);
        $this->inputCleaner = new InputCleaner();
        $this->outputCleaner = new OutputCleaner();
    }

    public function configure(array $options): void
    {
        $this->config->configure($options);
    }

    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);
        $input = $this->inputCleaner->clean($input);

        $this->domHandler->loadHtml($input);

        $root = $this->domHandler->getRoot();
        if (!$root) {
            return '';
        }

        $this->nodeSanitizer->sanitizeNode($root);

        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $this->domHandler->saveHtml($child);
        }

        return $this->outputCleaner->clean($output);
    }
}
