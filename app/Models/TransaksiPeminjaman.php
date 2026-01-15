<?php
// app/Models/TransaksiPeminjaman.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPeminjaman extends Model
{
    use HasFactory;

    protected $table = 'transaksi_peminjaman';
    protected $primaryKey = 'no_transaksi_pinjam';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_transaksi_pinjam',
        'kode_anggota',
        'nama_anggota',
        'tanggal_pinjam',
        'tanggal_batas_kembali',
        'kode_buku',
        'judul_buku',
        'jenis_bahan_pustaka',
        'jenis_koleksi',
        'jenis_media',
        'user_id',
        'status_peminjaman',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_batas_kembali' => 'date',
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

    // Methods
    public function isTerlambat()
    {
        return now()->gt($this->tanggal_batas_kembali) && $this->status_peminjaman === 'Dipinjam';
    }

    public function calculateDenda()
    {
        if (!$this->isTerlambat()) return 0;

        $kebijakan = Kebijakan::first();
        $hari_terlambat = now()->diffInDays($this->tanggal_batas_kembali);
        
        $denda = $hari_terlambat * $kebijakan->denda_per_hari;
        
        if ($kebijakan->batas_denda && $denda > $kebijakan->batas_denda) {
            return $kebijakan->batas_denda;
        }
        
        return $denda;
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Dipinjam' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            'Dikembalikan' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'Terlambat' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        ];

        $status = $this->isTerlambat() ? 'Terlambat' : $this->status_peminjaman;
        $class = $badges[$status] ?? 'bg-gray-100 text-gray-800';
        
        return "<span class='px-2 py-1 text-xs font-semibold rounded-full {$class}'>" . $status . "</span>";
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status_peminjaman', 'Dipinjam');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status_peminjaman', 'Dikembalikan');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status_peminjaman', 'Dipinjam')
                     ->where('tanggal_batas_kembali', '<', now()->toDateString());
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('no_transaksi_pinjam', 'like', "%{$search}%")
              ->orWhere('nama_anggota', 'like', "%{$search}%")
              ->orWhere('judul_buku', 'like', "%{$search}%")
              ->orWhere('kode_anggota', 'like', "%{$search}%")
              ->orWhere('kode_buku', 'like', "%{$search}%");
        });
    }
}