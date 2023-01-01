<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.login';

    public function getHeading(): string | Htmlable
    {
        return __('Sistem Informasi Masjid');
    }

    public function getSubheading(): string | Htmlable | null
    {
        return __('Silakan login untuk mengakses dashboard admin');
    }

    public function mount(): void
    {
        parent::mount();
    }

    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getUsernameFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getUsernameFormComponent(): Component
    {
        return TextInput::make('username')
            ->label(__('Username/Email/Phone'))
            ->required()
            ->autocomplete()
            ->autofocus()
            ->placeholder('Masukkan username/email/phone')
            ->validationAttribute('username')
            ->prefixIcon('heroicon-o-user', true)
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/login.form.password.label'))
            ->hint(filament()->hasPasswordReset() ? new HtmlString(Blade::render('<x-filament::link :href="filament()->getRequestPasswordResetUrl()" tabindex="3"> {{ __(\'filament-panels::pages/auth/login.actions.request_password_reset.label\') }}</x-filament::link>')) : null)
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required()
            ->validationAttribute('password')
            ->prefixIcon('heroicon-o-lock-closed', true)
            ->placeholder('Masukkan password')
            ->extraInputAttributes(['tabindex' => 2]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        $username = filter_var($data['username'], FILTER_VALIDATE_EMAIL) ? 'email' : (filter_var($data['username'], FILTER_VALIDATE_INT) ? 'phone' : 'username');

        return [
            $username => $data['username'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.username' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
