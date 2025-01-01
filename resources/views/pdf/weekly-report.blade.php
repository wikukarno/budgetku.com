<!DOCTYPE html>
<html>
<head>
    <title>
        Laporan Keuangan Mingguan - {{ $user->name }} ({{ $startDate->isoFormat('D MMMM Y') }} - {{ $endDate->isoFormat('D MMMM Y') }})
    </title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 100px;
            height: auto;
        }
        .header h1 {
            margin: 10px 0;
            font-size: 24px;
            color: #555;
        }
        .header p {
            margin: 0;
            font-size: 14px;
            color: #777;
        }
        .content {
            margin: 20px auto;
            max-width: 800px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .content h2 {
            margin-bottom: 10px;
            font-size: 18px;
            border-bottom: 2px solid #f4f4f9;
            padding-bottom: 5px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f9;
            color: #555;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
<div class="header">
    <img src="{{ $logo }}" alt="Logo">
    <p>Laporan Keuangan Mingguan</p>
</div>

<div class="content">
    <h2>Ringkasan Laporan</h2>
    <p><strong>Nama:</strong> {{ $user->name }}</p>
    <p><strong>Periode:</strong> {{ $startDate->isoFormat('D MMMM Y') }} - {{ $endDate->isoFormat('D MMMM Y') }}</p>
    <p><strong>Total Transaksi:</strong> Rp. {{ number_format($weeklyTotal, 0, ',', '.') }}</p>

    <h2>Detail Transaksi</h2>
    <table>
        <thead>
        <tr>
            <th>Tanggal</th>
            <th>Deskripsi</th>
            <th>Jumlah (Rp)</th>
        </tr>
        </thead>
        <tbody>
        @foreach($transactions as $transaction)
            <tr>
                <td>{{ \Carbon\Carbon::parse($transaction->purchase_date)->isoFormat('dddd, D MMMM Y') }}</td>
                <td>{{ $transaction->name_item }}</td>
                <td>Rp. {{ number_format($transaction->price, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="footer">
    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
</div>
</body>
</html>
