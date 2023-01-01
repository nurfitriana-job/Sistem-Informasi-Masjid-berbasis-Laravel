<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('about.title', 'About');
        $this->migrator->add('about.subtitle', 'Learn more about us');
        $this->migrator->add('about.description', 'This is a sample description for the about page.');
        $this->migrator->add('about.image', 'assets/images/masjid.webp');
        $this->migrator->add('about.button_text', 'Learn More');
        $this->migrator->add('about.button_link', 'https://example.com/learn-more');
        $this->migrator->add('about.show_button', true);
    }
};
