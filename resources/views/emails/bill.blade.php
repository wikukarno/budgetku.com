<x-mail::message>
    # Halo, {{ $user->name }}!

    Ini adalah pengingat bahwa tagihan bulanan Anda sudah tersedia.
    dan akan jatuh tempo pada tanggal {{ $due_date }}.

    <x-slot:subcopy>
            <table class="table">
            <thead>
                <tr>
                    <th scope="col">Nama</th>
                    <th scope="col">Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bills as $bill)
                    <tr>
                        <td>{{ $bill->nama_tagihan }}</td>
                        <td>Rp. {{ number_format($bill->harga_tagihan, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-slot:subcopy>

    Salam, Tim {{ config('app.name') }}
</x-mail::message>