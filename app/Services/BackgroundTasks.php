<?php

namespace App\Services;

use App\Models\User;
use Closure;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Bus\Batch;
use Illuminate\Bus\PendingBatch;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Bus;

class BackgroundTasks
{
    protected Notification $notification;

    protected PendingBatch | Batch $batch;

    protected User $user;

    protected Notification $successNotification;

    protected array | Collection $jobs;

    public function __construct(array | Collection $jobs, User $user)
    {
        $this->jobs = $jobs;
        $this->user = $user;

        $this->notification = Notification::make()
            ->body('Starting...')
            ->icon('heroicon-o-cog-8-tooth')
            ->info()
            ->persistent();

        $this->successNotification = Notification::make()
            ->title('Task executed successfully')
            ->success();
    }

    public static function make(array | Collection $jobs, User $user): self
    {
        return new self($jobs, $user);
    }

    public function setTitle(string | Closure | null $title): self
    {
        $this->notification->title($title);

        return $this;
    }

    public function setIcon(string | Htmlable | Closure | null $icon): self
    {
        $this->notification->icon($icon);

        return $this;
    }

    public function dispatch(): self
    {
        $notificationId = $this->notification->getId();
        $userId = $this->user->id;
        $successNotification = $this->successNotification->toArray();

        $this->batch = Bus::batch($this->jobs)
            ->progress(function (Batch $batch) use ($notificationId, $userId) {
                self::updateNotificationProgress($batch, $notificationId, $userId);
            })
            ->finally(function (Batch $batch) use ($notificationId, $userId, $successNotification) {
                self::updateNotificationProgress($batch, $notificationId, $userId, $successNotification);
            })
            ->dispatch();

        $this->notification->send();

        return $this;
    }

    public function setSuccessNotification(Closure $callable): self
    {
        $this->successNotification = $callable($this->successNotification);

        return $this;
    }

    public function getBatchId(): string
    {
        if ($this->batch instanceof PendingBatch) {
            throw new Exception('Batch has not been dispatched yet');
        }

        return $this->batch->id;
    }

    public function getNotificationId(): string
    {
        return $this->notification->getId();
    }

    public static function updateNotificationProgress(Batch $batch, string $notificationId, int $userId, ?array $successNotification = null): void
    {
        $body = $batch->progress() . '% done';

        Broadcast::private('App.Models.User.' . $userId)
            ->as('UpdateNotificationBody')
            ->with([
                'id' => $notificationId,
                'body' => $body,
            ])
            ->sendNow();

        if ($batch->finished()) {

            if ($successNotification) {
                Notification::fromArray($successNotification)->broadcast(User::find($userId));
            }
            Broadcast::private('App.Models.User.' . $userId)
                ->as('CloseNotification')
                ->with(['id' => $notificationId])
                ->send();
        }
    }
}
