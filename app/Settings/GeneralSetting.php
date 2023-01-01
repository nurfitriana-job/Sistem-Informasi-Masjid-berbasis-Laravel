<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSetting extends Settings
{
    public string $site_name;

    public ?string $site_logo;

    public ?string $site_favicon;

    public string $theme_color;

    public ?string $secondary_color;

    public string $logo_height;

    public string $sidebar_width;

    public ?string $site_support_email;

    public ?string $site_support_phone;

    public ?string $site_support_telegram;

    public ?string $google_analytics;

    public ?string $extra_javascript;

    public ?string $seo_title;

    public ?string $seo_description;

    public ?array $seo_metadata;

    public ?array $recaptcha_settings;

    public bool $site_active;

    public bool $registration_enabled;

    public bool $login_enabled;

    public bool $password_reset_enabled;

    public bool $sso_enabled;

    public bool $email_verification_enabled;

    public ?string $site_terms;

    public ?string $site_privacy;

    public ?array $site_social_links;

    public ?array $operating_hours;

    public ?string $site_address;

    public ?string $site_address_latitude;

    public ?string $site_address_longitude;

    public ?string $site_logo_dark;

    public static function group(): string
    {
        return 'GeneralSetting';
    }
}
