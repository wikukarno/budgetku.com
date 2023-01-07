<?php

if (!function_exists('sendText')) {
    function sendText($chatId, $text, $parse = "html")
    {
        $token = "5935118090:AAEgAmV0bV6Kr1U1vxdMwvprymEFy1YxVig";
        $path = "https://api.telegram.org/bot$token";
        file_get_contents($path . "/sendmessage?chat_id=" . $chatId . "&text=" . urlencode($text) . "&parse_mode=" . $parse);
    }
}