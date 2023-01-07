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
        $update = json_decode($request->getContent(), true);
        $chatId = $update['message']['chat']['id'];
        $message = $update['message']['text'];
        $date = Carbon::now()->toDateTimeString();

        if($message == '/start'){
            $response = 'Hello, Welcome to our bot';
        }else{
            $response = 'Sorry, I don\'t understand you';
        }

        $response = [
            'chat_id' => $chatId,
            'text' => $response,
            'date' => $date
        ];

    }
}
