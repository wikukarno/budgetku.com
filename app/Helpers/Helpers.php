<?php

if (!function_exists('sendText')) {
    function sendText($chatId, $text, $parse = "html")
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        if (!$token) {
            throw new \Exception('TELEGRAM_BOT_TOKEN is not configured');
        }
        $path = "https://api.telegram.org/bot$token";
        file_get_contents($path . "/sendmessage?chat_id=" . $chatId . "&text=" . urlencode($text) . "&parse_mode=" . $parse);
    }
}