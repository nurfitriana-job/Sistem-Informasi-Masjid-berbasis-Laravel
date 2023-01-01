<?php

use App\Http\Controllers\TelegramNotificationController;
use App\Livewire\Homepage;
use Illuminate\Support\Facades\Route;

Route::get('/', Homepage\Index::class)->name('homepage');

Route::middleware(['auth'])->group(function () {
    Route::post('/telegram/notification', [TelegramNotificationController::class, 'send'])
        ->name('send-notification');

    Route::get('/telegram/temp-url', [TelegramNotificationController::class, 'create'])
        ->name('telegram-temp-url');

    Route::delete('/telegram/notifications', [TelegramNotificationController::class, 'destroy'])
        ->name('disable-telegram-notifications');
});
