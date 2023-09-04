
<x-mail::message>
# Hallo Riska Oktaviana Putri,

Uang keluar sebesar Rp. {{ number_format($finance->price, 0, ',', '.') }} telah ditambahkan <br />
pada : {{ \Carbon\Carbon::parse($finance->created_at)->isoFormat('dddd, D MMMM Y') }} <br />
pukul : {{ \Carbon\Carbon::parse($finance->created_at)->isoFormat('HH:mm') }} WIB. <br />
nama barang: {{ $finance->name_item }}

Terima Kasih,<br>
{{ config('app.name') }}
</x-mail::message>
