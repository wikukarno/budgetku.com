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

        if(strpos($message, '/start') === 0) {
            $text = "Halo, Selamat datang di bot telegram saya. Silahkan ketik /help untuk melihat daftar perintah yang tersedia";
        } else if(strpos($message, '/help') === 0) {
            $text = "Daftar perintah yang tersedia:
            /start - Untuk memulai bot
            /help - Untuk melihat daftar perintah yang tersedia
            /date - Untuk melihat tanggal hari ini
            /time - Untuk melihat waktu sekarang
            /datetime - Untuk melihat tanggal dan waktu sekarang";
        } else if(strpos($message, '/date') === 0) {
            $text = "Tanggal hari ini adalah " . Carbon::now()->isoFormat('dddd, D MMMM Y');
        } else if(strpos($message, '/time') === 0) {
            $text = "Waktu sekarang adalah " . Carbon::now()->isoFormat('HH:mm:ss');
        } else if(strpos($message, '/datetime') === 0) {
            $text = "Tanggal dan waktu sekarang adalah " . Carbon::now()->isoFormat('dddd, D MMMM Y HH:mm:ss');
        } else {
            $text = "Maaf, perintah yang anda masukkan tidak tersedia. Silahkan ketik /help untuk melihat daftar perintah yang tersedia";
        }

        $url = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/sendMessage?chat_id=' . $chatId . '&text=' . $text;
        $response = file_get_contents($url);
        return $response;

    }
}
