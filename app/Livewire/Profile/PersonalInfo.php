<?php

namespace App\Livewire\Profile;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Jeffgreco13\FilamentBreezy\Livewire\PersonalInfo as PersonalInfoBreezy;

class PersonalInfo extends PersonalInfoBreezy
{
    public array $only = ['name', 'username', 'phone', 'email', 'telegram_chat_id', 'telegram_username'];

    protected function getProfileFormSchema(): array
    {
        $groupFields = Group::make([
            Hidden::make('telegram_chat_id'),
            $this->getNameComponent(),
            $this->getEmailComponent(),
            $this->getUsernameComponent(),
            $this->getPhoneComponent(),
        ])
            ->columns(2)
            ->columnSpanFull();

        return ($this->hasAvatars)
            ? [filament('filament-breezy')->getAvatarUploadComponent(), $groupFields]
            : [$groupFields];
    }

    protected function getPhoneComponent(): TextInput
    {
        return TextInput::make('phone')
            ->label(__('No. Telepon/HP'))
            ->tel()
            ->rules(['starts_with:62'])
            ->validationAttribute(__('No. Telepon/HP'))
            ->required();
    }

    protected function getUsernameComponent(): TextInput
    {
        return TextInput::make('username')
            ->label(__('Username'))
            ->required()
            ->unique($this->userClass, 'username', ignoreRecord: true)
            ->maxLength(20)
            ->validationAttribute(__('Username'));
    }
}
