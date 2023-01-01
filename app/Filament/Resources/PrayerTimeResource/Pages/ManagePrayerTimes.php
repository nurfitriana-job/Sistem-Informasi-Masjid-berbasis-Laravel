<?php

namespace App\Filament\Resources\PrayerTimeResource\Pages;

use App\Filament\Resources\PrayerTimeResource;
use Filament\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\HtmlString;

class ManagePrayerTimes extends ManageRecords
{
    protected static string $resource = PrayerTimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('getPrayerTime')
                ->label(__('Ambil Waktu Sholat'))
                ->slideOver()
                ->hidden(! auth()->user()->can('create', PrayerTimeResource::getModel()))
                ->form([
                    Placeholder::make('title')
                        ->hiddenLabel()
                        ->columnSpanFull()
                        ->content(fn () => new HtmlString(__('messages.prayer_time.title'))),
                    Select::make('city_id')
                        ->label(__('Kota'))
                        ->relationship('city', 'name')
                        ->preload()
                        ->required()
                        ->searchable(),
                ])
                ->action(function ($data) {
                    PrayerTimeResource::getModel()::getRows($data['city_id']);

                    Notification::make()
                        ->title(__('Data berhasil diperbarui'))
                        ->body(__('Waktu sholat berhasil diperbarui.'))
                        ->success()
                        ->send();
                }),
        ];
    }
}
