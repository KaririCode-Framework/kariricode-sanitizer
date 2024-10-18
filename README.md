# KaririCode Framework: Sanitizer Component

[![en](https://img.shields.io/badge/lang-en-red.svg)](README.md) [![pt-br](https://img.shields.io/badge/lang-pt--br-green.svg)](README.pt-br.md)

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white) ![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white) ![PHPUnit](https://img.shields.io/badge/PHPUnit-3776AB?style=for-the-badge&logo=php&logoColor=white)

A robust and flexible data sanitization component for PHP, part of the KaririCode Framework. It utilizes configurable processors and native functions to ensure data integrity and security in your applications.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
  - [Basic Usage](#basic-usage)
  - [Advanced Usage: Blog Post Sanitization](#advanced-usage-blog-post-sanitization)
- [Available Sanitizers](#available-sanitizers)
- [Configuration](#configuration)
- [Integration with Other KaririCode Components](#integration-with-other-kariricode-components)
- [Development and Testing](#development-and-testing)
- [Contributing](#contributing)
- [License](#license)
- [Support and Community](#support-and-community)

## Features

- Flexible attribute-based sanitization for object properties
- Comprehensive set of built-in sanitizers for common use cases
- Easy integration with other KaririCode components
- Configurable processors for customized sanitization logic
- Support for fallback values in case of sanitization failures
- Extensible architecture allowing custom sanitizers
- Robust error handling and reporting

## Installation

You can install the Sanitizer component via Composer:

```bash
composer require kariricode/sanitizer
```

### Requirements

- PHP 8.3 or higher
- Composer

## Usage

### Basic Usage

1. Define your data class with sanitization attributes:

```php
use KaririCode\Sanitizer\Attribute\Sanitize;

class UserProfile
{
    #[Sanitize(processors: ['trim', 'html_special_chars'])]
    private string $name = '';

    #[Sanitize(processors: ['trim', 'normalize_line_breaks'])]
    private string $email = '';

    // Getters and setters...
}
```

2. Set up the sanitizer and use it:

```php
use KaririCode\ProcessorPipeline\ProcessorRegistry;
use KaririCode\Sanitizer\Sanitizer;
use KaririCode\Sanitizer\Processor\Input\TrimSanitizer;
use KaririCode\Sanitizer\Processor\Input\HtmlSpecialCharsSanitizer;
use KaririCode\Sanitizer\Processor\Input\NormalizeLineBreaksSanitizer;

$registry = new ProcessorRegistry();
$registry->register('sanitizer', 'trim', new TrimSanitizer());
$registry->register('sanitizer', 'html_special_chars', new HtmlSpecialCharsSanitizer());
$registry->register('sanitizer', 'normalize_line_breaks', new NormalizeLineBreaksSanitizer());

$sanitizer = new Sanitizer($registry);

$userProfile = new UserProfile();
$userProfile->setName("  John Doe  ");
$userProfile->setEmail("john.doe@example.com\r\n");

$result = $sanitizer->sanitize($userProfile);

echo $userProfile->getName(); // Output: "John Doe"
echo $userProfile->getEmail(); // Output: "john.doe@example.com\n"

// Access sanitization results
print_r($result['sanitizedValues']);
print_r($result['messages']);
print_r($result['errors']);
```

### Advanced Usage: Blog Post Sanitization

Here's a more comprehensive example demonstrating how to use the KaririCode Sanitizer in a real-world scenario, such as sanitizing blog post content:

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use KaririCode\ProcessorPipeline\ProcessorRegistry;
use KaririCode\Sanitizer\Attribute\Sanitize;
use KaririCode\Sanitizer\Processor\Domain\HtmlPurifierSanitizer;
use KaririCode\Sanitizer\Processor\Domain\MarkdownSanitizer;
use KaririCode\Sanitizer\Processor\Input\HtmlSpecialCharsSanitizer;
use KaririCode\Sanitizer\Processor\Input\NormalizeLineBreaksSanitizer;
use KaririCode\Sanitizer\Processor\Input\StripTagsSanitizer;
use KaririCode\Sanitizer\Processor\Input\TrimSanitizer;
use KaririCode\Sanitizer\Processor\Security\XssSanitizer;
use KaririCode\Sanitizer\Sanitizer;

class BlogPost
{
    #[Sanitize(
        processors: ['trim', 'html_special_chars', 'xss_sanitizer'],
        messages: [
            'trim' => 'Title was trimmed',
            'html_special_chars' => 'Special characters in title were escaped',
            'xss_sanitizer' => 'XSS attempt was removed from title',
        ]
    )]
    private string $title = '';

    #[Sanitize(
        processors: ['trim', 'normalize_line_breaks'],
        messages: [
            'trim' => 'Slug was trimmed',
            'normalize_line_breaks' => 'Line breaks in slug were normalized',
        ]
    )]
    private string $slug = '';

    #[Sanitize(
        processors: ['trim', 'markdown', 'html_purifier'],
        messages: [
            'trim' => 'Content was trimmed',
            'markdown' => 'Markdown in content was processed',
            'html_purifier' => 'HTML in content was purified',
        ]
    )]
    private string $content = '';

    #[Sanitize(
        processors: ['trim', 'strip_tags', 'html_special_chars'],
        messages: [
            'trim' => 'Author name was trimmed',
            'strip_tags' => 'HTML tags were removed from author name',
            'html_special_chars' => 'Special characters in author name were escaped',
        ]
    )]
    private string $authorName = '';

    // Getters and setters...
}

// Set up the sanitizer
$registry = new ProcessorRegistry();
$registry->register('sanitizer', 'trim', new TrimSanitizer());
$registry->register('sanitizer', 'html_special_chars', new HtmlSpecialCharsSanitizer());
$registry->register('sanitizer', 'normalize_line_breaks', new NormalizeLineBreaksSanitizer());
$registry->register('sanitizer', 'strip_tags', new StripTagsSanitizer());
$registry->register('sanitizer', 'markdown', new MarkdownSanitizer());
$registry->register('sanitizer', 'xss_sanitizer', new XssSanitizer());

// Configure HTML Purifier with specific settings for blog content
$htmlPurifier = new HtmlPurifierSanitizer();
$htmlPurifier->configure([
    'allowedTags' => ['p', 'br', 'strong', 'em', 'u', 'ol', 'ul', 'li', 'a', 'img', 'h2', 'h3', 'blockquote'],
    'allowedAttributes' => ['href' => ['a'], 'src' => ['img'], 'alt' => ['img']],
]);
$registry->register('sanitizer', 'html_purifier', $htmlPurifier);

$sanitizer = new Sanitizer($registry);

// Simulating form submission with potentially unsafe data
$blogPost = new BlogPost();
$blogPost->setTitle("  Exploring KaririCode: A Modern PHP Framework <script>alert('xss')</script>  ");
$blogPost->setSlug(" exploring-kariricode-a-modern-php-framework \r\n");
$blogPost->setContent("
# Introduction

KaririCode is a **powerful** and _flexible_ PHP framework designed for modern web development.

<script>alert('malicious code');</script>

## Key Features

1. Robust sanitization
2. Efficient routing
3. Powerful ORM

Check out our [official website](https://kariricode.org) for more information!

<img src=\"harmful.jpg\" onerror=\"alert('xss')\" />
");
$blogPost->setAuthorName("<b>John Doe</b> <script>alert('xss')</script>");

$result = $sanitizer->sanitize($blogPost);

// Access sanitized data
echo $blogPost->getTitle(); // Sanitized title
echo $blogPost->getContent(); // Sanitized content

// Access sanitization details
print_r($result['sanitizedValues']);
print_r($result['messages']);
print_r($result['errors']);
```

This example demonstrates how to use the KaririCode Sanitizer to clean and secure blog post data, including handling of Markdown content, HTML purification, and protection against XSS attacks.

## Available Sanitizers

The Sanitizer component provides various built-in sanitizers:

### Input Sanitizers

- TrimSanitizer: Removes whitespace from the beginning and end of a string
- HtmlSpecialCharsSanitizer: Converts special characters to HTML entities
- NormalizeLineBreaksSanitizer: Standardizes line breaks across different operating systems
- StripTagsSanitizer: Removes HTML and PHP tags from a string

### Domain Sanitizers

- HtmlPurifierSanitizer: Sanitizes HTML content using the HTML Purifier library
- JsonSanitizer: Validates and prettifies JSON strings
- MarkdownSanitizer: Sanitizes Markdown content

### Security Sanitizers

- FilenameSanitizer: Ensures filenames are safe for use in file systems
- SqlInjectionSanitizer: Protects against SQL injection attacks
- XssSanitizer: Prevents Cross-Site Scripting (XSS) attacks

For detailed information on each sanitizer, including configuration options and usage examples, please refer to the [documentation](https://kariricode.org/docs/sanitizer).

## Configuration

The Sanitizer component can be configured globally or per-sanitizer basis. Here's an example of how to configure the `HtmlPurifierSanitizer`:

```php
use KaririCode\Sanitizer\Processor\Domain\HtmlPurifierSanitizer;

$htmlPurifier = new HtmlPurifierSanitizer();
$htmlPurifier->configure([
    'allowedTags' => ['p', 'br', 'strong', 'em'],
    'allowedAttributes' => ['href' => ['a'], 'src' => ['img']],
]);

$registry->register('sanitizer', 'html_purifier', $htmlPurifier);
```

For global configuration options, refer to the `Sanitizer` class constructor.

## Integration with Other KaririCode Components

The Sanitizer component is designed to work seamlessly with other KaririCode components:

- **KaririCode\Contract**: Provides interfaces and contracts for consistent component integration.
- **KaririCode\ProcessorPipeline**: Utilized for building and executing sanitization pipelines.
- **KaririCode\PropertyInspector**: Used for analyzing and processing object properties with sanitization attributes.

Example of integration:

```php
use KaririCode\ProcessorPipeline\ProcessorRegistry;
use KaririCode\ProcessorPipeline\ProcessorBuilder;
use KaririCode\PropertyInspector\AttributeAnalyzer;
use KaririCode\PropertyInspector\AttributeHandler;
use KaririCode\PropertyInspector\Utility\PropertyInspector;
use KaririCode\Sanitizer\Sanitizer;

$registry = new ProcessorRegistry();
// Register sanitizers...

$builder = new ProcessorBuilder($registry);
$attributeHandler = new AttributeHandler('sanitizer', $builder);
$propertyInspector = new PropertyInspector(new AttributeAnalyzer(Sanitize::class));

$sanitizer = new Sanitizer($registry);
```

## Development and Testing

For development and testing purposes, this package uses Docker and Docker Compose to ensure consistency across different environments. A Makefile is provided for convenience.

### Prerequisites

- Docker
- Docker Compose
- Make (optional, but recommended for easier command execution)

### Development Setup

1. Clone the repository:

   ```bash
   git clone https://github.com/KaririCode-Framework/kariricode-sanitizer.git
   cd kariricode-sanitizer
   ```

2. Set up the environment:

   ```bash
   make setup-env
   ```

3. Start the Docker containers:

   ```bash
   make up
   ```

4. Install dependencies:
   ```bash
   make composer-install
   ```

### Available Make Commands

- `make up`: Start all services in the background
- `make down`: Stop and remove all containers
- `make build`: Build Docker images
- `make shell`: Access the PHP container shell
- `make test`: Run tests
- `make coverage`: Run test coverage with visual formatting
- `make cs-fix`: Run PHP CS Fixer to fix code style
- `make quality`: Run all quality commands (cs-check, test, security-check)

For a full list of available commands, run:

```bash
make help
```

## Contributing

We welcome contributions to the KaririCode Sanitizer component! Here's how you can contribute:

1. Fork the repository
2. Create a new branch for your feature or bug fix
3. Write tests for your changes
4. Implement your changes
5. Run the test suite and ensure all tests pass
6. Submit a pull request with a clear description of your changes

Please read our [Contributing Guide](CONTRIBUTING.md) for more details on our code of conduct and development process.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support and Community

- **Documentation**: [https://kariricode.org/docs/sanitizer](https://kariricode.org/docs/sanitizer)
- **Issue Tracker**: [GitHub Issues](https://github.com/KaririCode-Framework/kariricode-sanitizer/issues)
- **Community Forum**: [KaririCode Club Community](https://kariricode.club)
- **Stack Overflow**: Tag your questions with `kariricode-sanitizer`

---

Built with ❤️ by the KaririCode team. Empowering developers to create more secure and robust PHP applications.
