<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests;

use KaririCode\Sanitizer\Attribute\Sanitize;

/**
 * UserProfile class represents a user's profile with sanitized data.
 *
 * This class demonstrates the usage of various sanitizers for different
 * types of user input data like name, email, age and biography.
 */
class UserProfile
{
    /**
     * User's full name - sanitized to remove HTML and XSS threats.
     */
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

    /**
     * User's email address - normalized and trimmed.
     */
    #[Sanitize(
        processors: ['trim', 'normalize_line_breaks', 'email_sanitizer'],
        messages: [
            'trim' => 'Email was trimmed',
            'normalize_line_breaks' => 'Line breaks in email were normalized',
            'email_sanitizer' => 'Email format was validated and sanitized',
        ]
    )]
    private string $email = '';

    /**
     * User's age - stripped of HTML tags and trimmed.
     */
    #[Sanitize(
        processors: ['trim', 'strip_tags', 'numeric_sanitizer'],
        messages: [
            'trim' => 'Age was trimmed',
            'strip_tags' => 'HTML tags were removed from age',
            'numeric_sanitizer' => 'Age was validated as numeric',
        ]
    )]
    private string $age = '';

    /**
     * User's biography - sanitized for HTML and markdown content.
     */
    #[Sanitize(
        processors: ['trim', 'html_purifier', 'markdown'],
        messages: [
            'trim' => 'Bio was trimmed',
            'html_purifier' => 'HTML was purified in bio',
            'markdown' => 'Markdown in bio was processed',
        ]
    )]
    private string $bio = '';

    /**
     * Phone number - normalized and formatted.
     */
    #[Sanitize(
        processors: ['trim', 'phone_sanitizer'],
        messages: [
            'trim' => 'Phone number was trimmed',
            'phone_sanitizer' => 'Phone number was formatted',
        ]
    )]
    private string $phone = '';

    /**
     * Website URL - validated and sanitized.
     */
    #[Sanitize(
        processors: ['trim', 'url_sanitizer'],
        messages: [
            'trim' => 'Website URL was trimmed',
            'url_sanitizer' => 'URL was validated and sanitized',
        ]
    )]
    private string $website = '';

    /**
     * Social media handle - alphanumeric sanitization.
     */
    #[Sanitize(
        processors: ['trim', 'alphanumeric_sanitizer'],
        messages: [
            'trim' => 'Social media handle was trimmed',
            'alphanumeric_sanitizer' => 'Handle was sanitized to alphanumeric',
        ]
    )]
    private string $socialHandle = '';

    /**
     * User's address - sanitized for special characters and line breaks.
     */
    #[Sanitize(
        processors: ['trim', 'normalize_line_breaks', 'html_special_chars'],
        messages: [
            'trim' => 'Address was trimmed',
            'normalize_line_breaks' => 'Address line breaks were normalized',
            'html_special_chars' => 'Special characters in address were escaped',
        ]
    )]
    private string $address = '';

    /**
     * Constructor initializes default values.
     */
    public function __construct()
    {
        $this->name = '';
        $this->email = '';
        $this->age = '';
        $this->bio = '';
        $this->phone = '';
        $this->website = '';
        $this->socialHandle = '';
        $this->address = '';
    }

    /**
     * Get user's name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set user's name.
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get user's email.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set user's email.
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get user's age.
     */
    public function getAge(): string
    {
        return $this->age;
    }

    /**
     * Set user's age.
     */
    public function setAge(string $age): self
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get user's biography.
     */
    public function getBio(): string
    {
        return $this->bio;
    }

    /**
     * Set user's biography.
     */
    public function setBio(string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * Get user's phone number.
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * Set user's phone number.
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get user's website.
     */
    public function getWebsite(): string
    {
        return $this->website;
    }

    /**
     * Set user's website.
     */
    public function setWebsite(string $website): self
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get user's social media handle.
     */
    public function getSocialHandle(): string
    {
        return $this->socialHandle;
    }

    /**
     * Set user's social media handle.
     */
    public function setSocialHandle(string $socialHandle): self
    {
        $this->socialHandle = $socialHandle;

        return $this;
    }

    /**
     * Get user's address.
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * Set user's address.
     */
    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Convert user profile to array.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'age' => $this->age,
            'bio' => $this->bio,
            'phone' => $this->phone,
            'website' => $this->website,
            'socialHandle' => $this->socialHandle,
            'address' => $this->address,
        ];
    }

    /**
     * Create UserProfile from array.
     */
    public static function fromArray(array $data): self
    {
        $profile = new self();

        if (isset($data['name'])) {
            $profile->setName($data['name']);
        }
        if (isset($data['email'])) {
            $profile->setEmail($data['email']);
        }
        if (isset($data['age'])) {
            $profile->setAge($data['age']);
        }
        if (isset($data['bio'])) {
            $profile->setBio($data['bio']);
        }
        if (isset($data['phone'])) {
            $profile->setPhone($data['phone']);
        }
        if (isset($data['website'])) {
            $profile->setWebsite($data['website']);
        }
        if (isset($data['socialHandle'])) {
            $profile->setSocialHandle($data['socialHandle']);
        }
        if (isset($data['address'])) {
            $profile->setAddress($data['address']);
        }

        return $profile;
    }

    /**
     * Create string representation of UserProfile.
     */
    public function __toString(): string
    {
        return sprintf(
            "UserProfile(name='%s', email='%s', age='%s')",
            $this->name,
            $this->email,
            $this->age
        );
    }
}
