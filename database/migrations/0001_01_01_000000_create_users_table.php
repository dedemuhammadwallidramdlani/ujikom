<?php
// database/migrations/0001_01_01_000000_create_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengguna', 20)->unique()->nullable(); // HAPUS ->after('id')
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'petugas'])->default('petugas'); // HAPUS ->after('password')
            $table->string('status_akun', 20)->default('Aktif'); // HAPUS ->after('role')
            $table->string('no_telepon', 15)->nullable(); // HAPUS ->after('status_akun')
            $table->text('alamat')->nullable(); // HAPUS ->after('no_telepon')
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};