<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode_pengguna',   // ditambahkan
        'name',
        'email',
        'password',
        'role',           // ditambahkan
        'status_akun',    // ditambahkan
        'no_telepon',     // ditambahkan
        'alamat',         // ditambahkan
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',      // ditambahkan
            'status_akun' => 'string', // ditambahkan
        ];
    }

    // ================ CUSTOM METHODS ================

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is petugas
     */
    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    /**
     * Check if user account is active
     */
    public function isActive(): bool
    {
        return $this->status_akun === 'Aktif';
    }

    /**
     * Generate kode pengguna automatically
     */
    public static function generateKodePengguna(string $role = 'petugas'): string
    {
        $prefix = $role === 'admin' ? 'ADM' : 'PGW';
        $lastUser = self::where('kode_pengguna', 'like', $prefix . '%')
                       ->orderBy('kode_pengguna', 'desc')
                       ->first();

        if (!$lastUser) {
            return $prefix . '001';
        }

        $lastNumber = (int) substr($lastUser->kode_pengguna, 3);
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $newNumber;
    }

    /**
     * Get formatted phone number
     */
    public function getFormattedPhoneAttribute(): string
    {
        if (!$this->no_telepon) {
            return '-';
        }

        $phone = $this->no_telepon;
        if (str_starts_with($phone, '0')) {
            $phone = '+62' . substr($phone, 1);
        }

        return $phone;
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('status_akun', 'Aktif');
    }

    /**
     * Scope for admin users
     */
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope for petugas users
     */
    public function scopePetugas($query)
    {
        return $query->where('role', 'petugas');
    }

    /**
     * Scope for searching users
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('kode_pengguna', 'like', "%{$search}%")
              ->orWhere('no_telepon', 'like', "%{$search}%");
        });
    }

    // ================ RELATIONSHIPS ================

    /**
     * Get all transaksi peminjaman by this user
     */
    public function transaksiPeminjaman()
    {
        return $this->hasMany(TransaksiPeminjaman::class, 'user_id');
    }

    /**
     * Get all pengembalian by this user
     */
    public function pengembalian()
    {
        return $this->hasMany(Pengembalian::class, 'user_id');
    }

    // ================ ATTRIBUTE ACCESSORS ================

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        if ($this->status_akun === 'Aktif') {
            return '<span class="badge bg-success">Aktif</span>';
        }
        
        return '<span class="badge bg-secondary">Nonaktif</span>';
    }

    /**
     * Get role badge HTML
     */
    public function getRoleBadgeAttribute(): string
    {
        if ($this->role === 'admin') {
            return '<span class="badge bg-danger">Admin</span>';
        }
        
        return '<span class="badge bg-primary">Petugas</span>';
    }

    /**
     * Get initials from name
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        
        return substr($initials, 0, 2);
    }
}