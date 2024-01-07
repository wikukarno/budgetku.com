<x-mail::message>
    # Halo, {{ $salary->user->name }}!

    Uang sebesar Rp. {{ number_format($salary->salary, 0, ',', '.') }} baru saja masuk ke akun Anda. 🎉

    **Detail Transaksi:**
    - **Tanggal:** {{ \Carbon\Carbon::parse($salary->created_at)->isoFormat('dddd, D MMMM Y') }}
    - **Waktu:** {{ \Carbon\Carbon::parse($salary->created_at)->isoFormat('HH:mm') }} WIB
    - **Keterangan:** {{ $salary->description }}

    Terima kasih atas dedikasi dan kerja keras Anda! Jangan ragu untuk menghubungi kami jika Anda memiliki pertanyaan.

    Salam,
    Tim {{ config('app.name') }}
</x-mail::message>