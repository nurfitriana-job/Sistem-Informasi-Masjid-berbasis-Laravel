<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\AnnounceNotification;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendAnnouncementNotification implements ShouldQueue
{
    use Batchable;
    use Queueable;

    protected $user;

    protected $data;

    public function __construct(User $user, $data)
    {
        $this->user = $user;
        $this->data = $data;
    }

    public function handle()
    {
        $notification = new AnnounceNotification($this->data);
        $this->user->notify($notification);
    }
}
