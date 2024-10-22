<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/UserProfile.php';

use KaririCode\ProcessorPipeline\ProcessorRegistry;
use KaririCode\Sanitizer\Attribute\Sanitize;
use KaririCode\Sanitizer\Processor\Domain\HtmlPurifierSanitizer;
use KaririCode\Sanitizer\Processor\Domain\JsonSanitizer;
use KaririCode\Sanitizer\Processor\Domain\MarkdownSanitizer;
use KaririCode\Sanitizer\Processor\Input\HtmlSpecialCharsSanitizer;
use KaririCode\Sanitizer\Processor\Input\NormalizeLineBreaksSanitizer;
use KaririCode\Sanitizer\Processor\Input\StripTagsSanitizer;
use KaririCode\Sanitizer\Processor\Input\TrimSanitizer;
use KaririCode\Sanitizer\Processor\Security\FilenameSanitizer;
use KaririCode\Sanitizer\Processor\Security\SqlInjectionSanitizer;
use KaririCode\Sanitizer\Processor\Security\XssSanitizer;
use KaririCode\Sanitizer\Sanitizer;

$registry = new ProcessorRegistry();
$registry->register('sanitizer', 'trim', new TrimSanitizer());
$registry->register('sanitizer', 'html_special_chars', new HtmlSpecialCharsSanitizer());
$registry->register('sanitizer', 'normalize_line_breaks', new NormalizeLineBreaksSanitizer());
$registry->register('sanitizer', 'strip_tags', new StripTagsSanitizer());
$registry->register('sanitizer', 'html_purifier', new HtmlPurifierSanitizer());
$registry->register('sanitizer', 'json', new JsonSanitizer());
$registry->register('sanitizer', 'markdown', new MarkdownSanitizer());
$registry->register('sanitizer', 'filename', new FilenameSanitizer());
$registry->register('sanitizer', 'sql_injection', new SqlInjectionSanitizer());
$registry->register('sanitizer', 'xss_sanitizer', new XssSanitizer());
$registry->register('sanitizer', 'email_sanitizer', new EmailSanitizer());
$registry->register('sanitizer', 'numeric_sanitizer', new NumericSanitizer());
$registry->register('sanitizer', 'phone_sanitizer', new PhoneSanitizer());
$registry->register('sanitizer', 'url_sanitizer', new UrlSanitizer());
$registry->register('sanitizer', 'alphanumeric_sanitizer', new AlphanumericSanitizer());

$autoSanitizer = new Sanitizer($registry);

// Create input objects with potentially unsafe data
$userProfile = new UserProfile();
$userProfile->setName("  Walmir Silva <script>alert('xss')</script>  ");
$userProfile->setEmail(" walmir.silva@example.com \r\n");
$userProfile->setAge(' <b>35</b> ');
$userProfile->setBio("# Hello\n\n<p>I'm Walmir!</p><script>alert('bio')</script>");

/**
 * Display original and sanitized values for an object.
 *
 * @param object $object The object to display values for
 * @param Sanitizer $sanitizer The sanitizer instance
 */
function displayValues(object $object, Sanitizer $sanitizer): void
{
    // Display original values
    echo "Original values:\n";
    $reflection = new ReflectionClass($object);
    foreach ($reflection->getProperties() as $property) {
        $propertyName = $property->getName();
        $getter = 'get' . ucfirst($propertyName);
        if (method_exists($object, $getter)) {
            echo ucfirst($propertyName) . ': "' . str_replace("\n", '\n', $object->$getter()) . "\"\n";
        }
    }

    // Sanitize the object and get the result
    $result = $sanitizer->sanitize($object);
    $sanitizedData = $result->getSanitizedData();

    // Display sanitized values
    echo "\nSanitized values:\n";
    foreach ($reflection->getProperties() as $property) {
        $propertyName = $property->getName();
        if (isset($sanitizedData[$propertyName])) {
            echo ucfirst($propertyName) . ': "' . str_replace("\n", '\n', $sanitizedData[$propertyName]) . "\"\n";
        }
    }

    // Display processing details if there are any
    if ($result->toArray()['sanitizedData']) {
        echo "\nSanitization details:\n";
        foreach ($result->toArray()['sanitizedData'] as $property => $value) {
            echo ucfirst($property) . ":\n";
            echo '  Value: ' . json_encode($value) . "\n";
        }
    }

    // Display any errors that occurred during sanitization
    if ($result->hasErrors()) {
        echo "\nErrors:\n";
        foreach ($result->getErrors() as $property => $errors) {
            echo ucfirst($property) . ":\n";
            foreach ($errors as $error) {
                echo "  - [{$error['errorKey']}] {$error['message']}\n";
            }
        }
    }

    echo "\n";
}

// Display and sanitize values for the user profile
echo "User Profile:\n";
displayValues($userProfile, $autoSanitizer);
