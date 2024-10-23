# KaririCode Framework: Sanitizer Component

A robust and flexible data sanitization component for PHP, part of the KaririCode Framework. It utilizes configurable processors and native functions to ensure data integrity and security in your applications.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
  - [Basic Usage](#basic-usage)
  - [Advanced Usage: Blog Post Sanitization](#advanced-usage-blog-post-sanitization)
- [Available Sanitizers](#available-sanitizers)
  - [Input Sanitizers](#input-sanitizers)
  - [Domain Sanitizers](#domain-sanitizers)
  - [Security Sanitizers](#security-sanitizers)
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
- Chainable sanitization pipelines for complex data transformations
- Built-in support for multiple character encodings
- Protection against XSS and SQL injection attacks

## Installation

You can install the Sanitizer component via Composer:

```bash
composer require kariricode/sanitizer
```

### Requirements

- PHP 8.3 or higher
- Composer
- Extensions: `ext-mbstring`, `ext-dom`, `ext-libxml`

## Usage

### Basic Usage

1. Define your data class with sanitization attributes:

```php
use KaririCode\Sanitizer\Attribute\Sanitize;

class UserProfile
{
    #[Sanitize(processors: ['trim', 'html_special_chars'])]
    private string $name = '';

    #[Sanitize(processors: ['trim', 'email_sanitizer'])]
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
use KaririCode\Sanitizer\Processor\Input\EmailSanitizer;

$registry = new ProcessorRegistry();
$registry->register('sanitizer', 'trim', new TrimSanitizer());
$registry->register('sanitizer', 'html_special_chars', new HtmlSpecialCharsSanitizer());
$registry->register('sanitizer', 'email_sanitizer', new EmailSanitizer());

$sanitizer = new Sanitizer($registry);

$userProfile = new UserProfile();
$userProfile->setName("  Walmir Silva <script>alert('xss')</script>  ");
$userProfile->setEmail(" walmir.silva@gmail.con ");

$result = $sanitizer->sanitize($userProfile);

echo $userProfile->getName(); // Output: "Walmir Silva"
echo $userProfile->getEmail(); // Output: "walmir.silva@gmail.com"
```

### Advanced Usage: Blog Post Sanitization

Here's an example of how to use the KaririCode Sanitizer in a real-world scenario, such as sanitizing blog post content:

```php
use KaririCode\Sanitizer\Attribute\Sanitize;

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
        processors: ['trim', 'markdown', 'html_purifier'],
        messages: [
            'trim' => 'Content was trimmed',
            'markdown' => 'Markdown in content was processed',
            'html_purifier' => 'HTML in content was purified',
        ]
    )]
    private string $content = '';

    // Getters and setters...
}

// Usage example
$blogPost = new BlogPost();
$blogPost->setTitle("  Exploring KaririCode: A Modern PHP Framework <script>alert('xss')</script>  ");
$blogPost->setContent("# Introduction\nKaririCode is a **powerful** and _flexible_ PHP framework designed for modern web development.");

$result = $sanitizer->sanitize($blogPost);

// Access sanitized data
echo $blogPost->getTitle(); // Sanitized title
echo $blogPost->getContent(); // Sanitized content
```

## Available Sanitizers

### Input Sanitizers

- **TrimSanitizer**: Removes whitespace from the beginning and end of a string.

  - **Configuration Options**:
    - `characterMask`: Specifies which characters to trim. Default is whitespace.
    - `trimLeft`: Boolean to trim from the left side. Default is `true`.
    - `trimRight`: Boolean to trim from the right side. Default is `true`.

- **HtmlSpecialCharsSanitizer**: Converts special characters to HTML entities to prevent XSS attacks.

  - **Configuration Options**:
    - `flags`: Configurable flags like `ENT_QUOTES | ENT_HTML5`.
    - `encoding`: Character encoding, e.g., 'UTF-8'.
    - `doubleEncode`: Boolean to prevent double encoding. Default is `true`.

- **NormalizeLineBreaksSanitizer**: Standardizes line breaks across different operating systems.

  - **Configuration Options**:
    - `lineEnding`: Specifies line ending style. Options: 'unix', 'windows', 'mac'.

- **EmailSanitizer**: Validates and corrects common email typos, normalizes email format, and handles whitespace.

  - **Configuration Options**:
    - `removeMailtoPrefix`: Boolean to remove 'mailto:' prefix. Default is `false`.
    - `typoReplacements`: Associative array of common typo replacements.
    - `domainReplacements`: Corrects commonly misspelled domain names.

- **PhoneSanitizer**: Formats and validates phone numbers, including international support and custom formatting options.

  - **Configuration Options**:
    - `applyFormat`: Boolean to apply formatting. Default is `false`.
    - `format`: Custom format pattern for phone numbers.
    - `placeholder`: Placeholder character used in formatting.

- **AlphanumericSanitizer**: Removes non-alphanumeric characters, with configurable options to allow certain special characters.

  - **Configuration Options**:
    - `allowSpace`, `allowUnderscore`, `allowDash`, `allowDot`: Boolean options to allow specific characters.
    - `preserveCase`: Boolean to maintain case sensitivity.

- **UrlSanitizer**: Validates and normalizes URLs, ensuring proper protocol and structure.

  - **Configuration Options**:
    - `enforceProtocol`: Enforces a specific protocol, e.g., 'https://'.
    - `defaultProtocol`: The protocol to apply if none is present.
    - `removeTrailingSlash`: Boolean to remove trailing slash.

- **NumericSanitizer**: Ensures that the input is a numeric value, with options for decimal and negative numbers.

  - **Configuration Options**:
    - `allowDecimal`, `allowNegative`: Boolean options to allow decimals and negative values.
    - `decimalSeparator`: Specifies the character used for decimals.

- **StripTagsSanitizer**: Removes HTML and PHP tags from input, with configurable options for allowed tags.
  - **Configuration Options**:
    - `allowedTags`: List of HTML tags to keep.
    - `keepSafeAttributes`: Boolean to keep certain safe attributes.
    - `safeAttributes`: Array of attributes to preserve.

### Domain Sanitizers

- **HtmlPurifierSanitizer**: Sanitizes HTML content by removing unsafe tags and attributes, ensuring safe HTML rendering.

  - **Configuration Options**:
    - `allowedTags`: Specifies which tags are allowed.
    - `allowedAttributes`: Defines allowed attributes for each tag.
    - `removeEmptyTags`, `removeComments`: Boolean to remove empty tags or HTML comments.
    - `htmlEntities`: Convert characters to HTML entities. Default is `true`.

- **JsonSanitizer**: Validates and prettifies JSON strings, removes invalid characters, and ensures proper JSON structure.

  - **Configuration Options**:
    - `prettyPrint`: Boolean to format JSON for readability.
    - `removeInvalidCharacters`: Boolean to remove invalid characters from JSON.
    - `validateUnicode`: Boolean to validate Unicode characters.

- **MarkdownSanitizer**: Processes and sanitizes Markdown content, escaping special characters and preserving the Markdown structure.
  - **Configuration Options**:
    - `allowedElements`: Specifies allowed Markdown elements (e.g., 'p', 'h1', 'a').
    - `escapeSpecialCharacters`: Boolean to escape special characters like '\*', '\_', etc.
    - `preserveStructure`: Boolean to maintain Markdown formatting.

### Security Sanitizers

- **FilenameSanitizer**: Ensures filenames are safe for use in file systems by removing unsafe characters and validating extensions.

  - **Configuration Options**:
    - `replacement`: Character used to replace unsafe characters. Default is `'-'`.
    - `preserveExtension`: Boolean to keep the file extension.
    - `blockDangerousExtensions`: Boolean to block extensions like '.exe', '.js'.
    - `allowedExtensions`: Array of allowed extensions.

- **SqlInjectionSanitizer**: Protects against SQL injection attacks by escaping special characters and removing potentially harmful content.

  - **Configuration Options**:
    - `escapeMap`: Array of characters to escape.
    - `removeComments`: Boolean to strip SQL comments.
    - `escapeQuotes`: Boolean to escape quotes in SQL queries.

- **XssSanitizer**: Prevents Cross-Site Scripting (XSS) attacks by removing malicious scripts, attributes, and ensuring safe HTML output.
  - **Configuration Options**:
    - `removeScripts`: Boolean to remove `<script>` tags.
    - `removeEventHandlers`: Boolean to remove 'on\*' event handlers.
    - `encodeHtmlEntities`: Boolean to encode unsafe characters.

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

## Registry Explanation

The registry is a core part of how sanitizers are managed within the KaririCode Framework. It acts as a centralized location to register and configure all sanitizers you plan to use in your application.

Here's how you can create and configure the registry:

```php
// Create and configure the registry
$registry = new ProcessorRegistry();

// Register all required processors
$registry->register('sanitizer', 'trim', new TrimSanitizer());
$registry->register('sanitizer', 'html_special_chars', new HtmlSpecialCharsSanitizer());
$registry->register('sanitizer', 'normalize_line_breaks', new NormalizeLineBreaksSanitizer());
$registry->register('sanitizer', 'html_purifier', new HtmlPurifierSanitizer());
$registry->register('sanitizer', 'markdown', new MarkdownSanitizer());
$registry->register('sanitizer', 'numeric_sanitizer', new NumericSanitizer());
$registry->register('sanitizer', 'email_sanitizer', new EmailSanitizer());
$registry->register('sanitizer', 'phone_sanitizer', new PhoneSanitizer());
$registry->register('sanitizer', 'url_sanitizer', new UrlSanitizer());
$registry->register('sanitizer', 'alphanumeric_sanitizer', new AlphanumericSanitizer());
$registry->register('sanitizer', 'filename_sanitizer', new FilenameSanitizer());
$registry->register('sanitizer', 'json_sanitizer', new JsonSanitizer());
$registry->register('sanitizer', 'xss_sanitizer', new XssSanitizer());
$registry->register('sanitizer', 'sql_injection', new SqlInjectionSanitizer());
$registry->register('sanitizer', 'strip_tags', new StripTagsSanitizer());
```

This code demonstrates how to register various sanitizers with the registry, allowing you to easily manage which sanitizers are available throughout your application. Each sanitizer is given a unique identifier, which can then be referenced in attributes to apply specific sanitization rules.

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
