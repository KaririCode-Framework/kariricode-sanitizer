<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use KaririCode\ProcessorPipeline\ProcessorRegistry;
use KaririCode\Sanitizer\Attribute\Sanitize;
use KaririCode\Sanitizer\Processor\Domain\HtmlPurifierSanitizer;
use KaririCode\Sanitizer\Processor\Domain\JsonSanitizer;
use KaririCode\Sanitizer\Processor\Domain\MarkdownSanitizer;
use KaririCode\Sanitizer\Processor\Input\AlphanumericSanitizer;
use KaririCode\Sanitizer\Processor\Input\EmailSanitizer;
use KaririCode\Sanitizer\Processor\Input\HtmlSpecialCharsSanitizer;
use KaririCode\Sanitizer\Processor\Input\NormalizeLineBreaksSanitizer;
use KaririCode\Sanitizer\Processor\Input\NumericSanitizer;
use KaririCode\Sanitizer\Processor\Input\PhoneSanitizer;
use KaririCode\Sanitizer\Processor\Input\TrimSanitizer;
use KaririCode\Sanitizer\Processor\Input\UrlSanitizer;
use KaririCode\Sanitizer\Processor\Security\FilenameSanitizer;
use KaririCode\Sanitizer\Processor\Security\XssSanitizer;
use KaririCode\Sanitizer\Sanitizer;

class JobApplication
{
    #[Sanitize(
        processors: [
            'trim',
            'html_purifier' => [
                'allowedTags' => [],
                'allowedAttributes' => [],
            ],
            'xss_sanitizer',
        ]
    )]
    private string $fullName = '';

    #[Sanitize(
        processors: [
            'trim',
            'email_sanitizer' => [
                'removeMailtoPrefix' => true,
                'typoReplacements' => [
                    '@gmail.con' => '@gmail.com',
                    '@yaho.com' => '@yahoo.com',
                ],
            ],
        ]
    )]
    private string $email = '';

    #[Sanitize(
        processors: [
            'trim',
            'phone_sanitizer' => [
                'applyFormat' => true,
                'format' => '(##) #####-####',
                'placeholder' => '#',
            ],
        ]
    )]
    private string $phone = '';

    #[Sanitize(
        processors: [
            'trim',
            'html_purifier' => [
                'allowedTags' => ['h2', 'p', 'ul', 'li', 'a'],
                'allowedAttributes' => ['href' => ['a']],
            ],
        ]
    )]
    private string $professionalSummary = '';

    #[Sanitize(
        processors: [
            'trim',
            'numeric_sanitizer' => [
                'allowDecimal' => false,
                'allowNegative' => false,
            ],
        ]
    )]
    private string $yearsOfExperience = '';

    #[Sanitize(
        processors: [
            'trim',
            'url_sanitizer' => [
                'enforceProtocol' => true,
                'defaultProtocol' => 'https://',
                'removeTrailingSlash' => true,
            ],
        ]
    )]
    private string $portfolioUrl = '';

    #[Sanitize(
        processors: [
            'trim',
            'alphanumeric_sanitizer' => [
                'allowUnderscore' => true,
                'allowDash' => true,
                'preserveCase' => false,
            ],
        ]
    )]
    private string $githubHandle = '';

    #[Sanitize(
        processors: [
            'trim',
            'alphanumeric_sanitizer' => [
                'allowUnderscore' => true,
                'allowDash' => false,
                'preserveCase' => false,
            ],
        ]
    )]
    private string $linkedinHandle = '';

    #[Sanitize(
        processors: [
            'trim',
            'filename_sanitizer' => [
                'replacement' => '-',
                'preserveExtension' => true,
                'allowedChars' => ['a-z', 'A-Z', '0-9', '-', '_', ' '],
            ],
        ]
    )]
    private string $resumeFileName = '';

    #[Sanitize(
        processors: [
            'trim',
            'json_sanitizer',
        ]
    )]
    private string $projectsJson = '';

    // Getters and Setters
    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $value): self
    {
        $this->fullName = $value;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $value): self
    {
        $this->email = $value;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $value): self
    {
        $this->phone = $value;

        return $this;
    }

    public function getProfessionalSummary(): string
    {
        return $this->professionalSummary;
    }

    public function setProfessionalSummary(string $value): self
    {
        $this->professionalSummary = $value;

        return $this;
    }

    public function getYearsOfExperience(): string
    {
        return $this->yearsOfExperience;
    }

    public function setYearsOfExperience(string $value): self
    {
        $this->yearsOfExperience = $value;

        return $this;
    }

    public function getPortfolioUrl(): string
    {
        return $this->portfolioUrl;
    }

    public function setPortfolioUrl(string $value): self
    {
        $this->portfolioUrl = $value;

        return $this;
    }

    public function getGithubHandle(): string
    {
        return $this->githubHandle;
    }

    public function setGithubHandle(string $value): self
    {
        $this->githubHandle = $value;

        return $this;
    }

    public function getLinkedinHandle(): string
    {
        return $this->linkedinHandle;
    }

    public function setLinkedinHandle(string $value): self
    {
        $this->linkedinHandle = $value;

        return $this;
    }

    public function getResumeFileName(): string
    {
        return $this->resumeFileName;
    }

    public function setResumeFileName(string $value): self
    {
        $this->resumeFileName = $value;

        return $this;
    }

    public function getProjectsJson(): string
    {
        return $this->projectsJson;
    }

    public function setProjectsJson(string $value): self
    {
        $this->projectsJson = $value;

        return $this;
    }
}

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

$sanitizer = new FilenameSanitizer();
$sanitizer->configure([
    'maxLength' => 100,
    'toLowerCase' => true,
    'allowedExtensions' => ['jpg', 'png', 'pdf'],
    'blockDangerousExtensions' => true,
]);

echo $sanitizer->process('My File Name.php') . "\n";  // Retorna "my_file_name"
echo $sanitizer->process('Document.PDF');      // Retorna "document.pdf"

// // Create the sanitizer
// $sanitizer = new Sanitizer($registry);

// // Create an application with potentially dangerous data
// $application = new JobApplication();
// $application
//     ->setFullName("  Walmir Silva <script>alert('xss')</script>  ")
//     ->setEmail(" walmir.silva@gmail.con \n")
//     ->setPhone("11987654321")
//     ->setProfessionalSummary("
// <h2>Professional Summary</h2>

// <p>I am a senior developer with experience in:</p>

// <ul>
//     <li>PHP Development</li>
//     <li>Database Design</li>
//     <li>System Architecture</li>
// </ul>

// <p>Visit my website: <a href='https://example.com'>My Portfolio</a></p>
// ")
//     ->setYearsOfExperience("10")
//     ->setPortfolioUrl("example.com/portfolio")
//     ->setGithubHandle("@walmir-silva")
//     ->setLinkedinHandle("Walmir-Silva")
//     ->setResumeFileName("Walmir Silva Resume (2024).pdf")
//     ->setProjectsJson('{
//         "projects": [
//             {
//                 "name": "E-commerce Platform",
//                 "role": "Lead Developer",
//                 "duration": "2 years"
//             }
//         ]
//     }');

// // Function to display the results
// function displayResults(JobApplication $application, array $result): void
// {
//     echo "Job Application Sanitization Results:\n";
//     echo "=====================================\n\n";

//     echo "Sanitized Values:\n";
//     echo "----------------\n";

//     // Display all sanitized values with clear formatting
//     echo sprintf("Full Name: %s\n", $application->getFullName());
//     echo sprintf("Email: %s\n", $application->getEmail());
//     echo sprintf("Phone: %s\n", $application->getPhone());
//     echo sprintf("Years of Experience: %s\n", $application->getYearsOfExperience());
//     echo sprintf("Portfolio URL: %s\n", $application->getPortfolioUrl());
//     echo sprintf("GitHub Handle: %s\n", $application->getGithubHandle());
//     echo sprintf("LinkedIn Handle: %s\n", $application->getLinkedinHandle());
//     echo sprintf("Resume Filename: %s\n", $application->getResumeFileName());

//     echo "\nProfessional Summary:\n";
//     echo "-------------------\n";
//     echo $application->getProfessionalSummary() . "\n\n";

//     echo "Projects JSON:\n";
//     echo "-------------\n";
//     echo $application->getProjectsJson() . "\n";
// }

// // Sanitize the application and display results
// $result = $sanitizer->sanitize($application);
// displayResults($application, $result->toArray());
