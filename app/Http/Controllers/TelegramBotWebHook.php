<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class TelegramBotWebHook extends Controller
{
    public function index()
    {
        $url = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/setWebhook?url=' . env('TELEGRAM_BOT_WEBHOOK_URL');
        $response = file_get_contents($url);
        return $response;
    }

    public function webhook(Request $request)
    {

        $update = json_decode(file_get_contents('php://input'), true);
        $chatId = '781699306';
        $message = $update['message']['text'];
        $fromFirstName = "Wiku Karno";
        $sekarang = date("Y-m-d H:i:s");
        if (strpos($message, "/start") === 0 || strpos($message, "/mulai") === 0) {
            $text = "Halo $fromFirstName, Selamat datang di Bot Telegram Saya. \n\n";
            sendText($chatId, $text);
            // Set Start Return
        }
    }
}
