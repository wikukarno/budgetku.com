@component('mail::message')
    # Halo, {{ $user->name }}

    Berikut adalah ringkasan laporan keuangan Anda untuk periode:

    **{{ $startDate->isoFormat('dddd, D MMMM Y') }}** hingga **{{ $endDate->isoFormat('dddd, D MMMM Y') }}**

    **Total Transaksi Mingguan:** Rp{{ number_format($weeklyTotal, 0, ',', '.') }}

    Silahkan download lampiran PDF di bawah ini untuk melihat detail transaksi.

    Terima kasih telah menggunakan layanan kami. Jika Anda memiliki pertanyaan atau masukan, jangan ragu untuk menghubungi kami.

    Hormat kami,


    {{ config('app.name') }}
@endcomponent
