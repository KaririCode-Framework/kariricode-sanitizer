<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use KaririCode\ProcessorPipeline\ProcessorRegistry;
use KaririCode\Sanitizer\Attribute\Sanitize;
use KaririCode\Sanitizer\Processor\Cleaner\EmailAddressCleaner;
use KaririCode\Sanitizer\Processor\Cleaner\NumericValueCleaner;
use KaririCode\Sanitizer\Processor\HtmlPurifier;
use KaririCode\Sanitizer\Processor\Remover\WhitespaceRemover;
use KaririCode\Sanitizer\Processor\XssSanitizer;
use KaririCode\Sanitizer\Sanitizer;

class UserInput
{
    #[Sanitize(sanitizers: ['trim', 'html_purifier', 'xss_sanitizer'])]
    private string $name = '';

    #[Sanitize(sanitizers: ['trim', 'email_cleaner'])]
    private string $email = '';

    #[Sanitize(sanitizers: ['trim', 'numeric_value_cleaner'])]
    private string $age = '';

    #[Sanitize(sanitizers: ['trim', 'html_purifier'], fallbackValue: 'No bio provided')]
    private string $bio = '';

    // Getters and setters
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

// Set up the ProcessorRegistry
$registry = new ProcessorRegistry();
$registry->register('sanitizer', 'trim', new WhitespaceRemover());
$registry->register('sanitizer', 'html_purifier', new HtmlPurifier());
$registry->register('sanitizer', 'email_cleaner', new EmailAddressCleaner());
$registry->register('sanitizer', 'numeric_value_cleaner', new NumericValueCleaner());
$registry->register('sanitizer', 'xss_sanitizer', new XssSanitizer());

$autoSanitizer = new Sanitizer($registry);

// Create a UserInput object with potentially unsafe data
$userInput = new UserInput();
$userInput->setName("  John Doe <script>alert('xss')</script>  ");
$userInput->setEmail(' john.doe@example#.com ');
$userInput->setAge(' 25 years old ');
$userInput->setBio("<p>Hello, I'm John!</p><script>alert('bio')</script>");

// Display original values
echo "Original values:\n";
echo 'Name: ' . $userInput->getName() . "\n";
echo 'Email: ' . $userInput->getEmail() . "\n";
echo 'Age: ' . $userInput->getAge() . "\n";
echo 'Bio: ' . $userInput->getBio() . "\n\n";

// Sanitize the user input
$autoSanitizer->sanitize($userInput);

// Display sanitized values
echo "Sanitized values:\n";
echo 'Name: ' . $userInput->getName() . "\n";
echo 'Email: ' . $userInput->getEmail() . "\n";
echo 'Age: ' . $userInput->getAge() . "\n";
echo 'Bio: ' . $userInput->getBio() . "\n";
