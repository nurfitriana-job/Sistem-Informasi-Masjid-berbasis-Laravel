<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('GeneralSetting.site_name', config('app.name'));
        $this->migrator->add('GeneralSetting.site_active', true);
        $this->migrator->add('GeneralSetting.site_logo', null);
        $this->migrator->add('GeneralSetting.site_logo_dark', null);
        $this->migrator->add('GeneralSetting.site_favicon', null);
        $this->migrator->add('GeneralSetting.logo_height', '5rem');
        $this->migrator->add('GeneralSetting.sidebar_width', '16rem');
        $this->migrator->add('GeneralSetting.theme_color', '#fbc50b');
        $this->migrator->add('GeneralSetting.secondary_color', '#007d3a');
        $this->migrator->add('GeneralSetting.site_support_email', null);
        $this->migrator->add('GeneralSetting.site_support_phone', null);
        $this->migrator->add('GeneralSetting.site_support_telegram', null);
        $this->migrator->add('GeneralSetting.site_address', null);
        $this->migrator->add('GeneralSetting.site_address_latitude', null);
        $this->migrator->add('GeneralSetting.site_address_longitude', null);
        $this->migrator->add('GeneralSetting.site_terms', null);
        $this->migrator->add('GeneralSetting.site_privacy', null);
        $this->migrator->add('GeneralSetting.site_social_links', [
            'facebook' => null,
            'twitter' => null,
            'instagram' => null,
            'youtube' => null,
            'tiktok' => null,
        ]);
        $this->migrator->add('GeneralSetting.operating_hours', [
            'monday' => '08:00 - 17:00',
            'tuesday' => '08:00 - 17:00',
            'wednesday' => '08:00 - 17:00',
            'thursday' => '08:00 - 17:00',
            'friday' => '08:00 - 17:00',
            'saturday' => '08:00 - 17:00',
            'sunday' => '08:00 - 17:00',
        ]);
        $this->migrator->add('GeneralSetting.registration_enabled', true);
        $this->migrator->add('GeneralSetting.login_enabled', true);
        $this->migrator->add('GeneralSetting.password_reset_enabled', true);
        $this->migrator->add('GeneralSetting.email_verification_enabled', true);
        $this->migrator->add('GeneralSetting.sso_enabled', true);
        $this->migrator->add('GeneralSetting.google_analytics', null);
        $this->migrator->add('GeneralSetting.extra_javascript', null);
        $this->migrator->add('GeneralSetting.seo_title', null);
        $this->migrator->add('GeneralSetting.seo_description', null);
        $this->migrator->add('GeneralSetting.seo_metadata', [
            'keywords' => null,
            'author' => null,
            'robots' => null,
            'canonical' => null,
        ]);
        $this->migrator->add('GeneralSetting.recaptcha_settings', [
            'recaptcha_enabled' => false,
            'recaptcha_site_key' => null,
            'recaptcha_secret_key' => null,
        ]);
    }
};
