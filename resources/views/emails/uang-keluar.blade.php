<x-mail::message>
    # Halo {{ $finance->user->name  }},

    Uang keluar sebesar Rp. {{ number_format($finance->price, 0, ',', '.') }} telah dicatat pada:

    - **Tanggal:** {{ \Carbon\Carbon::parse($finance->created_at)->isoFormat('dddd, D MMMM Y') }}
    - **Waktu:** {{ \Carbon\Carbon::parse($finance->created_at)->isoFormat('HH:mm') }} WIB
    - **Nama Barang:** {{ $finance->name_item }}

    Terima kasih atas pencatatan Anda!

    Salam,
    {{ config('app.name') }}
</x-mail::message>