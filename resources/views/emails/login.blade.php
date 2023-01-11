@props([
'url' => 'https://api-wikukarno.wikukarno.id/amankan',
])
<x-mail::message>
# Hallo {{ $user->name }},

Terdeteksi login pada {{ \Carbon\Carbon::parse($user->last_login_at)->isoFormat('dddd, D MMMM Y') }} pukul {{ \Carbon\Carbon::parse($user->last_login_at)->isoFormat('HH:mm') }} WIB. <br>
dengan alamat IP {{ $user->last_login_ip }}.

Jika bukan anda yang melakukan login,
Silahkan klik tombol dibawah ini untuk mengamankan akun anda.

<x-mail::button :url="$url" :color>
    Amankan Akun Sekarang
</x-mail::button>


Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
