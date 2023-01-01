<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Helpers\MediaTypeHelper;
use App\Models\Event;
use App\Models\NotificationTemplate;
use App\Models\User;
use App\Notifications\TelegramNotification;
use App\Settings\GeneralSetting;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Log;

class ManageEvents extends ManageRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->after(function ($record) {
                    $this->sendEventNotificationToUsers($record);
                }),
        ];
    }

    /**
     * Kirim notifikasi kegiatan ke semua pengguna yang memiliki Telegram chat ID
     *
     * @param  mixed  $event
     */
    protected function sendEventNotificationToUsers($event): void
    {
        // Ambil template notifikasi
        $template = NotificationTemplate::where('name', 'kegiatan_masjid_create')
            ->where('is_active', true)
            ->first();

        if (! $template) {
            Log::warning('Template notifikasi kegiatan_masjid_create tidak ditemukan');

            return;
        }

        $placeholders = $this->preparePlaceholdersForEvent($event);

        $message = $this->replacePlaceholders($template->body, $placeholders);

        $mediaData = $this->getMediaDataFromTemplate($event);

        // Kirim ke semua pengguna Telegram
        $this->sendTelegramNotificationsToUsers($message, $mediaData['url'], $mediaData['category']);
    }

    /**
     * Menyiapkan array placeholder untuk kegiatan
     *
     * @param  mixed  $event
     */
    protected function preparePlaceholdersForEvent($event): array
    {
        $generalSettings = app(GeneralSetting::class);

        return [
            '{name}' => $event->name,
            '{category_name}' => $event?->category?->name ?? 'Umum',
            '{status}' => $event->status ? 'Siap Diselenggarakan' : 'Mohon Menunggu Konfirmasi',
            '{description}' => $event->description,
            '{start_date}' => $event->start_date
                ? date('d F Y H:i', strtotime($event->start_date))
                : 'Belum Ditentukan',
            '{end_date}' => $event->end_date
                ? date('d F Y H:i', strtotime($event->end_date))
                : 'Selesai',
            '{masjid}' => $generalSettings->site_name,
            '{masjid_address}' => $generalSettings->site_address,
            '{user}' => $event?->user?->name ?? 'Admin',
            '{masjid_phone}' => $generalSettings->site_support_phone,
            '{masjid_email}' => $generalSettings->site_support_email,
            '{site_url}' => config('app.url'),
        ];
    }

    /**
     * Mengganti placeholder dalam template dengan nilai sebenarnya
     */
    protected function replacePlaceholders(string $template, array $placeholders): string
    {
        return str_replace(
            array_keys($placeholders),
            array_values($placeholders),
            $template
        );
    }

    /**
     * Mendapatkan data media dari template
     *
     * @param  NotificationTemplate  $template
     */
    protected function getMediaDataFromTemplate(Event $event): array
    {
        $mediaItem = $event->getFirstMedia('events');
        $mediaUrl = null;
        $mediaCategory = null;

        if ($mediaItem) {
            $mediaCategory = MediaTypeHelper::getCategoryFromMediaObject($mediaItem);
            $mediaUrl = $event->getFirstMediaUrl('events');
        }

        return [
            'url' => $mediaUrl,
            'category' => $mediaCategory,
        ];
    }

    /**
     * Kirim notifikasi Telegram ke semua pengguna yang terdaftar
     */
    protected function sendTelegramNotificationsToUsers(string $message, ?string $mediaUrl, ?string $mediaCategory): void
    {
        $users = User::whereNotNull('telegram_chat_id')->get();

        foreach ($users as $user) {
            $user->notify(new TelegramNotification(
                $message,
                $mediaUrl,
                $mediaCategory
            ));
        }
    }

    public function getTabs(): array
    {
        $tabs = [];
        $tabs[null] = Tab::make('All');

        $events = Event::with('category')->get();

        foreach ($events as $eventItem) {
            if ($eventItem?->category?->name) {
                $tabs[ucfirst($eventItem->category->name)] = Tab::make(ucfirst($eventItem->category->name))
                    ->query(fn ($query) => $query->where('category_id', $eventItem->category->id));
            }
        }

        return $tabs;
    }
}
