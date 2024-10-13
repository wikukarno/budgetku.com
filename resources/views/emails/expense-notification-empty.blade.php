@component('mail::message')
# Halo, {{ $user->name }}

Anda hari ini belum memiliki pengeluaran yang tercatat di aplikasi. Yuk, catat pengeluaran Anda sekarang!

<x-mail::button :url="$url" color="success">
    Catat Pengeluaran
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
