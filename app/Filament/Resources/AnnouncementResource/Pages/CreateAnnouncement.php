<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use App\Actions\Announce;
use App\Filament\Resources\AnnouncementResource;
use App\Models\User;
use App\Notifications\TelegramNotification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Facades\FilamentColor;

class CreateAnnouncement extends CreateRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected static bool $canCreateAnother = false;

    // customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('Data berhasil dipublikasikan ke semua pengguna terkait.');
    }

    public function afterCreate()
    {
        $record = $this->getRecord();

        $color = $record->color;
        $custom_color = $record->custom_color;
        $icon = $record->icon;
        $title = $record->title;
        $body = $record->body;

        $isNotifyToAll = in_array('all', $record->users);

        $users = $isNotifyToAll ? User::all() : User::query()->whereIn('id', $record->users)->get();

        $announce = Announce::make();

        if ($title) {
            $announce->title($title);
        }
        if ($body) {
            $announce->body($body);
        }
        if ($icon) {
            $announce->icon($icon);
        }

        if ($color && $color == 'custom') {
            $announce->color(str($custom_color)->remove('rgb(')->remove(')'));
        } else {
            $announce->color(FilamentColor::getColors()[$color]['500']);
        }

        $announce->announceTo($users);
        foreach ($users as $user) {
            if ($user->telegram_chat_id) {
                // check jika body memiliki url
                $media = null;
                if (str_contains($body, 'http://') || str_contains($body, 'https://')) {
                    // Menangkap URL dari body, misalnya mencari URL gambar
                    preg_match_all('/https?:\/\/[^\s]+/', $body, $matches);

                    if (isset($matches[0]) && count($matches[0]) > 0) {
                        $media = $matches[0][0];
                    }
                }
                $user->notify(new TelegramNotification(
                    "*$title*\n\n$body",
                    $media,
                    'photo',
                ));
            }
        }
    }
}
