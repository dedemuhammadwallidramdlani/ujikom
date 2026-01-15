<?php
// database/migrations/xxxx_create_pengembalian_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->string('no_transaksi_kembali', 20)->primary();
            $table->string('kode_anggota', 20);
            $table->string('nama_anggota', 100);
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali');
            $table->string('kode_buku', 20);
            $table->string('judul_buku', 200);
            $table->string('jenis_bahan_pustaka', 30)->nullable();
            $table->string('jenis_koleksi', 30)->nullable();
            $table->string('jenis_media', 30)->nullable();
            $table->decimal('denda', 12, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            // Foreign keys
            $table->foreign('kode_anggota')->references('kode_anggota')->on('anggota');
            $table->foreign('kode_buku')->references('kode_buku')->on('buku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalian');
    }
};