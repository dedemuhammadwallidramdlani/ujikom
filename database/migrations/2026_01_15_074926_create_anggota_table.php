<?php
// database/migrations/xxxx_create_anggota_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota', function (Blueprint $table) {
            $table->string('kode_anggota', 20)->primary();
            $table->string('nama_anggota', 100);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telepon', 15)->nullable();
            $table->string('jenis_identitas', 20)->nullable();
            $table->string('nomor_identitas', 50)->nullable();
            $table->string('jenis_anggota', 30)->nullable();
            $table->enum('status', ['Aktif', 'Nonaktif', 'Diblokir'])->default('Aktif');
            $table->integer('jumlah_pinjam')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};