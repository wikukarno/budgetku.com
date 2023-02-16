<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_tagihan',
        'harga_tagihan',
        'pemilik_tagihan', 
        'siklus_tagihan', // 1 bulanan, 2 tahunan
        'jatuh_tempo_tagihan',
        'metode_pembayaran',
        'keterangan_tagihan',
    ];
}
