<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        $chatId = $update['message']['chat']['id'];
        $message = $update['message']['text'];
        $fromFirstName = "Wiku Karno";
        $sekarang = date("Y-m-d H:i:s");
        if (strpos($message, "/start") === 0 || strpos($message, "/mulai") === 0) {
            $text = "Halo $fromFirstName, Selamat datang di Bot Telegram Saya. \n\n Sekarang jam $sekarang";
            sendText($chatId, $text);
        }
        // daftarkan telegram id
        if (strpos($message, "/daftar") === 0) {
            $email = str_replace("/daftar ", "", $message);
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->telegram_id = $chatId;
                $user->telegram_username = $fromFirstName;
                $user->save();
                $text = "Terima kasih $fromFirstName, akun kamu sudah terdaftar di Bot Telegram Saya.";
                sendText($chatId, $text);
            } else {
                $text = "Maaf $fromFirstName, akun kamu belum terdaftar di Bot Telegram Saya.";
                sendText($chatId, $text);
            }
        }
        if (strpos($message, "/id") === 0) {
            $text = "ID kamu adalah $chatId";
            sendText($chatId, $text);
        }
        // amankan akun
        if (strpos($message, "/amankan") === 0) {
            $user = User::where('telegram_id', $chatId)->first();
            $user->delete();

            if ($user) {
                $text = "Hallo $fromFirstName, akun anda berhasil kami amankan, \nkamu bisa mengaktifkan kembali akun kamu dengan cara /aktifkan";
                sendText($chatId, $text);
            } else {
                $text = "Maaf $fromFirstName, akun kamu belum terdaftar di Bot Telegram Saya.";
                sendText($chatId, $text);
            }
        }
        // aktifkan akun
        if (strpos($message, "/aktifkan") === 0) {
            $user = User::where('telegram_id', $chatId)->first();
            $user->deleted_at = null;
            $user->save();
            if ($user) {
                $text = "Hallo $fromFirstName, akun anda berhasil kami aktifkan kembali, \nTerimakasih.";
                sendText($chatId, $text);
            } else {
                $text = "Maaf $fromFirstName, akun kamu belum terdaftar di Bot Telegram Saya.";
                sendText($chatId, $text);
            }
        }
    }
}
