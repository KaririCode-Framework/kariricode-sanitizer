# KaririCode Framework: Sanitizer Component

[![en](https://img.shields.io/badge/lang-en-red.svg)](README.md) [![pt-br](https://img.shields.io/badge/lang-pt--br-green.svg)](README.pt-br.md)

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white) ![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white) ![PHPUnit](https://img.shields.io/badge/PHPUnit-3776AB?style=for-the-badge&logo=php&logoColor=white)

A robust and flexible data sanitization component for PHP, part of the KaririCode Framework. It utilizes configurable processors and native functions to ensure data integrity and security in your applications.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
  - [Basic Usage](#basic-usage)
  - [Advanced Usage](#advanced-usage)
- [Available Sanitizers](#available-sanitizers)
- [Integration with Other KaririCode Components](#integration-with-other-kariricode-components)
- [Development and Testing](#development-and-testing)
- [License](#license)
- [Support and Community](#support-and-community)

## Features

- Flexible attribute-based sanitization for object properties
- Comprehensive set of built-in sanitizers for common use cases
- Easy integration with other KaririCode components
- Configurable processors for customized sanitization logic
- Support for fallback values in case of sanitization failures
- Extensible architecture allowing custom sanitizers

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
    #[Sanitize(sanitizers: ['trim', 'html_special_chars'])]
    private string $name = '';

    #[Sanitize(sanitizers: ['trim', 'normalize_line_breaks'])]
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
$userProfile->setName("  Walmir Silva  ");
$userProfile->setEmail("walmir.silva@example.com\r\n");

$sanitizer->sanitize($userProfile);

echo $userProfile->getName(); // Output: "Walmir Silva"
echo $userProfile->getEmail(); // Output: "walmir.silva@example.com\n"
```

### Advanced Usage

You can create custom sanitizers by implementing the `Processor` or `ConfigurableProcessor` interfaces:

```php
use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;

class CustomSanitizer extends AbstractSanitizerProcessor implements ConfigurableProcessor
{
    private $option;

    public function configure(array $options): void
    {
        $this->option = $options['custom_option'] ?? 'default';
    }

    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);
        // Custom sanitization logic here
        return $input;
    }
}

// Register and use the custom sanitizer
$registry->register('sanitizer', 'custom', new CustomSanitizer());

class AdvancedProfile
{
    #[Sanitize(sanitizers: ['custom' => ['custom_option' => 'value']])]
    private string $customField = '';
}
```

## Available Sanitizers

The Sanitizer component provides various built-in sanitizers:

### Input Sanitizers

- TrimSanitizer
- HtmlSpecialCharsSanitizer
- NormalizeLineBreaksSanitizer
- StripTagsSanitizer

### Domain Sanitizers

- HtmlPurifierSanitizer
- JsonSanitizer
- MarkdownSanitizer

### Security Sanitizers

- FilenameSanitizer
- SqlInjectionSanitizer
- XssSanitizer

Each sanitizer is designed to handle specific types of data and security concerns. For detailed information on each sanitizer, please refer to the [documentation](https://kariricode.org/docs/sanitizer).

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

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support and Community

- **Documentation**: [https://kariricode.org/docs/sanitizer](https://kariricode.org/docs/sanitizer)
- **Issue Tracker**: [GitHub Issues](https://github.com/KaririCode-Framework/kariricode-sanitizer/issues)
- **Community**: [KaririCode Club Community](https://kariricode.club)

---

Built with ❤️ by the KaririCode team. Empowering developers to create more secure and robust PHP applications.
