<?php

namespace App\Livewire\Homepage;

use App\Settings\GeneralSetting;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $settings = app(GeneralSetting::class)?->site_active ?? false;
        if (! $settings) {
            return abort(503, 'Site is not active yet.');
        }

        return view('livewire.homepage.index')
            ->layout('components.layouts.homepage');
    }
}
