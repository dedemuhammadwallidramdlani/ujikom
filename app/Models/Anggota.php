<?php
// app/Models/Anggota.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggota';
    protected $primaryKey = 'kode_anggota';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_anggota',
        'nama_anggota',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'no_telepon',
        'jenis_identitas',
        'nomor_identitas',
        'jenis_anggota',
        'status',
        'jumlah_pinjam'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Relationships
    public function transaksiPeminjaman()
    {
        return $this->hasMany(TransaksiPeminjaman::class, 'kode_anggota', 'kode_anggota');
    }

    public function pengembalian()
    {
        return $this->hasMany(Pengembalian::class, 'kode_anggota', 'kode_anggota');
    }

    // Methods
    public function isAktif()
    {
        return $this->status === 'Aktif';
    }

    public function isDiblokir()
    {
        return $this->status === 'Diblokir';
    }

    public function canBorrow()
    {
        $kebijakan = Kebijakan::first();
        return $this->isAktif() && $this->jumlah_pinjam < $kebijakan->maksimal_jumlah_pinjam;
    }

    // Accessors
    public function getUmurAttribute()
    {
        if (!$this->tanggal_lahir) return null;
        return now()->diffInYears($this->tanggal_lahir);
    }

    public function getFormattedTanggalLahirAttribute()
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->translatedFormat('d F Y') : '-';
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nama_anggota', 'like', "%{$search}%")
              ->orWhere('kode_anggota', 'like', "%{$search}%")
              ->orWhere('no_telepon', 'like', "%{$search}%")
              ->orWhere('nomor_identitas', 'like', "%{$search}%");
        });
    }
}