<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AboutSetting extends Settings
{
    public string $title;

    public ?string $subtitle;

    public ?string $description;

    public ?string $image;

    public ?string $button_text;

    public ?string $button_link;

    public ?bool $show_button;

    public static function group(): string
    {
        return 'about';
    }
}
