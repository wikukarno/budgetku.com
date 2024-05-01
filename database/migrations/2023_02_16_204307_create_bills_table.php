<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('users_id');
            $table->string('nama_tagihan')->unique();
            $table->string('harga_tagihan');
            $table->string('siklus_tagihan');
            $table->dateTime('jatuh_tempo_tagihan');
            $table->string('metode_pembayaran');
            $table->string('keterangan_tagihan');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bills');
    }
};
