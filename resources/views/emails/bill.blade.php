<x-mail::message>
    # Halo, {{ $bill->pemilik_tagihan }}!

    Ini adalah pengingat bahwa tagihan bulanan Anda sudah tersedia.

    **Detail Tagihan:**
    - **Nama Tagihan:** {{ $bill->nama_tagihan }}
    - **Jumlah Tagihan:** Rp. {{ number_format($bill->harga_tagihan, 0, ',', '.') }}
    - **Tanggal Jatuh Tempo:** {{ \Carbon\Carbon::parse($bill->jatuh_tempo_tagihan)->isoFormat('dddd, D MMMM Y') }}

    Mohon lakukan pembayaran sebelum tanggal jatuh tempo untuk menghindari denda keterlambatan.

    Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi kami.

    Terima kasih atas perhatian Anda.

    Salam,
    Tim {{ config('app.name') }}
</x-mail::message>