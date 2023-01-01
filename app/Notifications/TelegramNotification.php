<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramFile;
use NotificationChannels\Telegram\TelegramLocation;
use NotificationChannels\Telegram\TelegramMessage;

class TelegramNotification extends Notification
{
    use Queueable;

    protected $message;

    protected $media;

    protected $mediaType;

    protected $latitude;

    protected $longitude;

    /**
     * Create a new notification instance.
     *
     * @param  string  $message
     * @param  mixed  $media  (URL or path)
     * @param  string  $mediaType  (optional: gif, video, photo, document, etc.)
     * @param  float|null  $latitude  (optional for location)
     * @param  float|null  $longitude  (optional for location)
     */
    public function __construct($message, $media = null, $mediaType = null, $latitude = null, $longitude = null)
    {
        $this->message = $message;
        $this->media = $media;
        $this->mediaType = $mediaType;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TelegramChannel::class];
    }

    /**
     * Get the Telegram representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return TelegramMessage
     */
    public function toTelegram($notifiable)
    {
        if (! $this->media) {
            $telegramMessage = TelegramMessage::create()
                ->to($notifiable?->telegram_chat_id)
                ->content($this->message);

            return $telegramMessage;
        }

        // Otherwise, handle media-based messages
        $telegramMessage = $this->handleMedia($notifiable);

        return $telegramMessage;
    }

    /**
     * Handles media attachment types (gif, video, photo, document, audio, or location).
     *
     * @param  TelegramMessage  $telegramMessage
     * @param  mixed  $notifiable
     * @return TelegramMessage
     */
    private function handleMedia($notifiable)
    {
        if (in_array($this->mediaType, ['gif', 'video', 'photo', 'document', 'audio'])) {
            $method = $this->getMediaMethod();
            if ($method) {
                $telegramMessage = TelegramFile::create()
                    ->to($notifiable->telegram_chat_id)
                    ->content($this->message)
                    ->$method($this->media);
            }
        } elseif ($this->latitude && $this->longitude) {
            $telegramMessage = TelegramLocation::create()
                ->latitude($this->latitude)
                ->longitude($this->longitude);
        }

        return $telegramMessage;
    }

    /**
     * Returns the method corresponding to the media type.
     *
     * @return string|null
     */
    private function getMediaMethod()
    {
        $mediaMethods = [
            'gif' => 'animation',
            'video' => 'video',
            'photo' => 'photo',
            'document' => 'document',
            'audio' => 'audio',
        ];

        return $mediaMethods[$this->mediaType] ?? null;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            // Define the data to be returned as an array
        ];
    }
}
