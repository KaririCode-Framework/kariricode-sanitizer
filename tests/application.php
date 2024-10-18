<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

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

class UserProfile
{
    #[Sanitize(
        processors: ['trim', 'html_purifier', 'xss_sanitizer', 'html_special_chars'],
        messages: [
            'trim' => 'Name was trimmed',
            'html_purifier' => 'HTML was purified in name',
            'xss_sanitizer' => 'XSS attempt was removed from name',
            'html_special_chars' => 'Special characters were escaped in name',
        ]
    )]
    private string $name = '';

    #[Sanitize(
        processors: ['trim', 'normalize_line_breaks'],
        messages: [
            'trim' => 'Email was trimmed',
            'normalize_line_breaks' => 'Line breaks in email were normalized',
        ]
    )]
    private string $email = '';

    #[Sanitize(
        processors: ['trim', 'strip_tags'],
        messages: [
            'trim' => 'Age was trimmed',
            'strip_tags' => 'HTML tags were removed from age',
        ]
    )]
    private string $age = '';

    #[Sanitize(
        processors: ['trim', 'html_purifier', 'markdown'],
        messages: [
            'trim' => 'Bio was trimmed',
            'html_purifier' => 'HTML was purified in bio',
            'markdown' => 'Markdown in bio was processed',
        ]
    )]
    private string $bio = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getAge(): string
    {
        return $this->age;
    }

    public function setAge(string $age): void
    {
        $this->age = $age;
    }

    public function getBio(): string
    {
        return $this->bio;
    }

    public function setBio(string $bio): void
    {
        $this->bio = $bio;
    }
}

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

$autoSanitizer = new Sanitizer($registry);

// Create input objects with potentially unsafe data
$userProfile = new UserProfile();
$userProfile->setName("  Walmir Silva <script>alert('xss')</script>  ");
$userProfile->setEmail(" walmir.silva@example.com \r\n");
$userProfile->setAge(' <b>35</b> ');
$userProfile->setBio("# Hello\n\n<p>I'm Walmir!</p><script>alert('bio')</script>");

// Function to display original and sanitized values
function displayValues($object, $sanitizer)
{
    echo "Original values:\n";
    $reflection = new ReflectionClass($object);
    foreach ($reflection->getProperties() as $property) {
        $propertyName = $property->getName();
        $getter = 'get' . ucfirst($propertyName);
        if (method_exists($object, $getter)) {
            echo ucfirst($propertyName) . ': "' . str_replace("\n", '\n', $object->$getter()) . "\"\n";
        }
    }

    $result = $sanitizer->sanitize($object);

    echo "\nSanitized values:\n";
    foreach ($reflection->getProperties() as $property) {
        $propertyName = $property->getName();
        $getter = 'get' . ucfirst($propertyName);
        if (method_exists($object, $getter)) {
            echo ucfirst($propertyName) . ': "' . str_replace("\n", '\n', $result['object']->$getter()) . "\"\n";
        }
    }

    if (!empty($result['sanitizedValues'])) {
        echo "\nSanitized values details:\n";
        foreach ($result['sanitizedValues'] as $property => $data) {
            echo ucfirst($property) . ":\n";
            if (!empty($data['messages'])) {
                foreach ($data['messages'] as $processorName => $message) {
                    echo "  - [$processorName] $message\n";
                }
            }
            echo '  Value: ' . json_encode($data['value']) . "\n";
        }
    }

    if (!empty($result['errors'])) {
        echo "\nErrors:\n";
        foreach ($result['errors'] as $property => $errors) {
            echo ucfirst($property) . ":\n";
            foreach ($errors as $error) {
                echo "  - $error\n";
            }
        }
    }

    echo "\n";
}

// Display and sanitize values for each object
echo "User Profile:\n";
displayValues($userProfile, $autoSanitizer);
