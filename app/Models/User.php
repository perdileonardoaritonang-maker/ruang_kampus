<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * Note: role and status_verifikasi are deliberately excluded to prevent mass assignment vulnerabilities.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_lengkap',
        'email',
        'password',
        'jenis_pengguna',
        'nomor_identitas',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'diverifikasi_pada' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Cek apakah role akun adalah Biro (Admin).
     */
    public function isBiro(): bool
    {
        return $this->role === 'biro';
    }

    /**
     * Cek apakah role akun adalah User (Mahasiswa / Dosen).
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Cek apakah identitas kampus sudah diverifikasi Biro.
     */
    public function isIdentityVerified(): bool
    {
        return $this->status_verifikasi === 'terverifikasi';
    }

    /**
     * Cek apakah user berhak meminjam ruangan (Email verified + Identitas approved + Akun aktif).
     */
    public function canBookRoom(): bool
    {
        return $this->is_active 
            && $this->hasVerifiedEmail() 
            && $this->isIdentityVerified();
    }

    /**
     * Relasi ke akun Biro yang melakukan verifikasi identitas.
     */
    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}
