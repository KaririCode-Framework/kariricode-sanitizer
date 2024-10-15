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
    #[Sanitize(sanitizers: ['trim', 'html_purifier', 'xss_sanitizer', 'html_special_chars'])]
    private string $name = '';

    #[Sanitize(sanitizers: ['trim', 'normalize_line_breaks'])]
    private string $email = '';

    #[Sanitize(sanitizers: ['trim', 'strip_tags'])]
    private string $age = '';

    #[Sanitize(sanitizers: ['trim', 'html_purifier', 'markdown'], fallbackValue: 'No bio provided')]
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

class UserPreferences
{
    #[Sanitize(sanitizers: ['json'])]
    private string $preferences = '';

    public function getPreferences(): string
    {
        return $this->preferences;
    }

    public function setPreferences(string $preferences): void
    {
        $this->preferences = $preferences;
    }
}

class UserAvatar
{
    #[Sanitize(sanitizers: ['filename'])]
    private string $avatarFilename = '';

    public function getAvatarFilename(): string
    {
        return $this->avatarFilename;
    }

    public function setAvatarFilename(string $avatarFilename): void
    {
        $this->avatarFilename = $avatarFilename;
    }
}

class UserSearch
{
    #[Sanitize(sanitizers: ['sql_injection'])]
    private string $searchQuery = '';

    public function getSearchQuery(): string
    {
        return $this->searchQuery;
    }

    public function setSearchQuery(string $searchQuery): void
    {
        $this->searchQuery = $searchQuery;
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

$userPreferences = new UserPreferences();
$userPreferences->setPreferences('{"theme": "dark", "notifications": true}');

$userAvatar = new UserAvatar();
$userAvatar->setAvatarFilename('my avatar!.jpg');

$userSearch = new UserSearch();
$userSearch->setSearchQuery("users'; DROP TABLE users; --");

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

    $sanitizer->sanitize($object);

    echo "\nSanitized values:\n";
    foreach ($reflection->getProperties() as $property) {
        $propertyName = $property->getName();
        $getter = 'get' . ucfirst($propertyName);
        if (method_exists($object, $getter)) {
            echo ucfirst($propertyName) . ': "' . str_replace("\n", '\n', $object->$getter()) . "\"\n";
        }
    }
    echo "\n";
}

// Display and sanitize values for each object
echo "User Profile:\n";
displayValues($userProfile, $autoSanitizer);

echo "User Preferences:\n";
displayValues($userPreferences, $autoSanitizer);

echo "User Avatar:\n";
displayValues($userAvatar, $autoSanitizer);

echo "User Search:\n";
displayValues($userSearch, $autoSanitizer);
