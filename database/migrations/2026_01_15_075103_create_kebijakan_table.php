<?php
// database/migrations/xxxx_create_kebijakan_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kebijakan', function (Blueprint $table) {
            $table->id();
            $table->integer('maksimal_waktu_pinjam')->default(7);
            $table->integer('maksimal_jumlah_pinjam')->default(3);
            $table->decimal('denda_per_hari', 8, 2)->default(5000);
            $table->integer('batas_denda')->nullable();
            $table->boolean('allow_reservasi')->default(true);
            $table->integer('masa_berlaku_anggota')->default(365);
            $table->integer('denda_hilang')->default(100000);
            $table->text('syarat_anggota')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kebijakan');
    }
};