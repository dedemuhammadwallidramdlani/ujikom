<?php
// app/Models/Pengembalian.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    use HasFactory;

    protected $table = 'pengembalian';
    protected $primaryKey = 'no_transaksi_kembali';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_transaksi_kembali',
        'kode_anggota',
        'nama_anggota',
        'tanggal_pinjam',
        'tanggal_kembali',
        'kode_buku',
        'judul_buku',
        'jenis_bahan_pustaka',
        'jenis_koleksi',
        'jenis_media',
        'denda',
        'keterangan',
        'user_id'
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali' => 'date',
        'denda' => 'decimal:2',
    ];

    // Relationships
    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'kode_anggota', 'kode_anggota');
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'kode_buku', 'kode_buku');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getHariTerlambatAttribute()
    {
        $kebijakan = Kebijakan::first();
        $tanggal_seharusnya_kembali = $this->tanggal_pinjam->addDays($kebijakan->maksimal_waktu_pinjam);
        
        if ($this->tanggal_kembali->gt($tanggal_seharusnya_kembali)) {
            return $this->tanggal_kembali->diffInDays($tanggal_seharusnya_kembali);
        }
        
        return 0;
    }

    // Scopes
    public function scopeWithDenda($query)
    {
        return $query->where('denda', '>', 0);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('no_transaksi_kembali', 'like', "%{$search}%")
              ->orWhere('nama_anggota', 'like', "%{$search}%")
              ->orWhere('judul_buku', 'like', "%{$search}%");
        });
    }

    public function scopePeriode($query, $start, $end)
    {
        if ($start && $end) {
            return $query->whereBetween('tanggal_kembali', [$start, $end]);
        }
        return $query;
    }
}