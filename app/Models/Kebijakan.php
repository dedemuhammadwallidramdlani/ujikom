<?php
// app/Models/Kebijakan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kebijakan extends Model
{
    use HasFactory;

    protected $table = 'kebijakan';
    
    protected $fillable = [
        'maksimal_waktu_pinjam',
        'maksimal_jumlah_pinjam',
        'denda_per_hari',
        'batas_denda',
        'allow_reservasi',
        'masa_berlaku_anggota',
        'denda_hilang',
        'syarat_anggota'
    ];

    protected $casts = [
        'allow_reservasi' => 'boolean',
    ];

    // Singleton pattern untuk mengambil kebijakan
    public static function getSettings()
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = self::create([
                'maksimal_waktu_pinjam' => 7,
                'maksimal_jumlah_pinjam' => 3,
                'denda_per_hari' => 5000,
                'allow_reservasi' => true,
                'masa_berlaku_anggota' => 365,
                'denda_hilang' => 100000,
            ]);
        }
        
        return $settings;
    }
}