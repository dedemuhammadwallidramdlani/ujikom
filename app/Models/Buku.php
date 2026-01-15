<?php
// app/Models/Buku.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'buku';
    protected $primaryKey = 'kode_buku';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_buku',
        'judul',
        'jenis_bahan_pustaka',
        'jenis_koleksi',
        'jenis_media',
        'pengarang',
        'penerbit',
        'tahun_terbit',
        'cetakan',
        'edisi',
        'status',
        'stok',
        'isbn',
        'deskripsi',
        'lokasi_rak'
    ];

    // Relationships
    public function transaksiPeminjaman()
    {
        return $this->hasMany(TransaksiPeminjaman::class, 'kode_buku', 'kode_buku');
    }

    public function pengembalian()
    {
        return $this->hasMany(Pengembalian::class, 'kode_buku', 'kode_buku');
    }

    // Methods
    public function isAvailable()
    {
        return $this->status === 'Tersedia' && $this->stok > 0;
    }

    public function isBorrowed()
    {
        return $this->status === 'Dipinjam';
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Tersedia' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'Dipinjam' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            'Rusak' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            'Hilang' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
        ];

        $class = $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
        return "<span class='px-2 py-1 text-xs font-semibold rounded-full {$class}'>" . $this->status . "</span>";
    }

    // Scopes
    public function scopeTersedia($query)
    {
        return $query->where('status', 'Tersedia')->where('stok', '>', 0);
    }

    public function scopeDipinjam($query)
    {
        return $query->where('status', 'Dipinjam');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%")
              ->orWhere('kode_buku', 'like', "%{$search}%")
              ->orWhere('pengarang', 'like', "%{$search}%")
              ->orWhere('penerbit', 'like', "%{$search}%")
              ->orWhere('isbn', 'like', "%{$search}%");
        });
    }

    public function scopeFilterByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeFilterByJenis($query, $jenis)
    {
        if ($jenis) {
            return $query->where('jenis_koleksi', $jenis);
        }
        return $query;
    }
}