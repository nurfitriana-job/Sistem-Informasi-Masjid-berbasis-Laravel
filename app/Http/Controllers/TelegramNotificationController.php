<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\TelegramNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramNotificationController extends Controller
{
    /**
     * Store Telegram Chat ID from telegram webhook message.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $updates = Telegram::getWebhookUpdate();

        try {
            $messageText = $updates->getMessage()->getText();
        } catch (Exception $e) {
            return response()->json([
                'code' => $e->getCode(),
                'message' => 'Accepted with error: \'' . $e->getMessage() . '\'',
            ], 202);
        }
        log::info('Telegram Webhook', [
            'message' => $messageText,
        ]);
        // Check if the message matches the expected pattern.
        if (! Str::of($messageText)->test('/^\/start\s[A-Za-z0-9]{35}$/')) {
            return response('Accepted', 202);
        }

        // Cleanup the string
        $userTempCode = Str::of($messageText)->remove('/start ')->toString();
        Log::info('Telegram Webhook', [
            'user_temp_code' => $userTempCode,
        ]);
        // Get the User ID from the cache using the temp code as key.
        $userId = Cache::store('telegram')->pull($userTempCode);
        $user = User::find($userId);

        // Get Telegram ID from the request.
        $chatId = $request->message['chat']['id'];

        log::info('Telegram Webhook', [
            'user_id' => $userId,
            'chat_id' => $chatId,
        ]);

        // Update user with the Telegram Chat ID
        $user->telegram_chat_id = $chatId;
        $user->save();

        return response('Success', 200);
    }

    public function send(Request $request)
    {
        $user = auth()->user();
        $user->notify(new TelegramNotification($request->notification));

        return back();
    }

    public function destroy()
    {
        $user = auth()->user();
        $user->telegram_chat_id = null;
        $user->save();

        return back();
    }
}
