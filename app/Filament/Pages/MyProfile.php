<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Notifications\TelegramNotification;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Jeffgreco13\FilamentBreezy\Pages\MyProfilePage;
use Livewire\Attributes\Renderless;

class MyProfile extends MyProfilePage
{
    protected function getActions(): array
    {
        $user = Filament::getCurrentPanel()->auth()->user();

        return [
            Actions\Action::make('telegram_notification')
                ->label(fn () => ! $user->telegram_chat_id ? __('Aktifkan Notifikasi Telegram') : __('Matikan Notifikasi Telegram'))
                ->action('telegramNotification')
                ->icon('heroicon-o-bell')
                ->color(fn () => ! $user->telegram_chat_id ? 'success' : 'danger')
                ->requiresConfirmation(),
            Actions\Action::make('test_notification')
                ->label(__('Kirim Notifikasi'))
                ->action('sendTestNotification')
                ->hidden(fn () => ! $user->telegram_chat_id)
                ->icon('heroicon-o-bell')
                ->color('primary')
                ->requiresConfirmation(),
        ];
    }

    public function sendTestNotification()
    {
        $user = Filament::getCurrentPanel()->auth()->user();
        $user->notify(new TelegramNotification('Test notifikasi dari Filament.'));

        Notification::make()
            ->title(__('Notifikasi Terkirim'))
            ->body(__('Notifikasi berhasil dikirim.'))
            ->success()
            ->send();
    }

    #[Renderless]
    public function telegramNotification()
    {
        $user = Filament::getCurrentPanel()->auth()->user();
        if ($user->telegram_chat_id) {
            $this->deleteTelegramNotification();

            return;
        }

        $telegramBotUrl = config('services.telegram-bot-api.bot_url');

        $userTempCode = Str::random(35);
        Cache::store('telegram')
            ->put($userTempCode, Filament::getCurrentPanel()->auth()->user()->id, $seconds = 120);

        // Telegram URL:
        // https://t.me/ExampleComBot?start=vCH1vGWJxfSeofSAs0K5PA
        $telegramUrl = $telegramBotUrl . '?start=' . $userTempCode;

        $this->js(
            "window.open('{$telegramUrl}', '_blank');"
        );

        Notification::make()
            ->title(__('Notifikasi Telegram'))
            ->body(__('Silakan buka Telegram dan klik tautan di atas untuk mengaktifkan notifikasi.'))
            ->actions([
                NotificationAction::make('open')
                    ->label(__('Buka Telegram'))
                    ->url($telegramUrl)
                    ->openUrlInNewTab()
                    ->icon('tabler-brand-telegram')
                    ->color('success'),
            ])
            ->success()
            ->send();
    }

    public function deleteTelegramNotification()
    {
        $user = User::find(Filament::getCurrentPanel()->auth()->user()->id);
        $user->telegram_chat_id = null;
        $user->save();

        Notification::make()
            ->title(__('Notifikasi Telegram'))
            ->body(__('Notifikasi Telegram berhasil dinonaktifkan.'))
            ->success()
            ->send();

        $this->js(
            'window.location.reload();'
        );
    }
}
