<?php
// database/migrations/xxxx_create_buku_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku', function (Blueprint $table) {
            $table->string('kode_buku', 20)->primary();
            $table->string('judul', 200);
            $table->string('jenis_bahan_pustaka', 30)->nullable();
            $table->string('jenis_koleksi', 30)->nullable();
            $table->string('jenis_media', 30)->nullable();
            $table->string('pengarang', 100)->nullable();
            $table->string('penerbit', 100)->nullable();
            $table->year('tahun_terbit')->nullable();
            $table->string('cetakan', 20)->nullable();
            $table->string('edisi', 20)->nullable();
            $table->enum('status', ['Tersedia', 'Dipinjam', 'Rusak', 'Hilang'])->default('Tersedia');
            $table->integer('stok')->default(1);
            $table->string('isbn', 20)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('lokasi_rak', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};