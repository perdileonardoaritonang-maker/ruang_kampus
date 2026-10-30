<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id',
        'target_role',
        'judul',
        'pesan',
        'tipe',
        'url',
        'dibaca',
        'dibaca_pada',
    ];

    protected function casts(): array
    {
        return [
            'dibaca' => 'boolean',
            'dibaca_pada' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope untuk mengambil notifikasi sesuai user yang sedang login.
     */
    public function scopeUntukUser($query, User $user)
    {
        if ($user->isBiro()) {
            return $query->where(function ($q) use ($user) {
                $q->where('target_role', 'biro')
                  ->orWhere('user_id', $user->id);
            });
        }

        return $query->where('user_id', $user->id);
    }

    /**
     * Scope notifikasi yang belum dibaca.
     */
    public function scopeBelumDibaca($query)
    {
        return $query->where('dibaca', false);
    }

    /**
     * Kirim notifikasi ke Biro.
     */
    public static function kirimKeBiro(string $judul, string $pesan, string $tipe, ?string $url = null): self
    {
        return static::create([
            'user_id' => null,
            'target_role' => 'biro',
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe,
            'url' => $url,
            'dibaca' => false,
        ]);
    }

    /**
     * Kirim notifikasi ke User tertentu.
     */
    public static function kirimKeUser(int $userId, string $judul, string $pesan, string $tipe, ?string $url = null): self
    {
        return static::create([
            'user_id' => $userId,
            'target_role' => 'user',
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe,
            'url' => $url,
            'dibaca' => false,
        ]);
    }

    /**
     * Tandai notifikasi sebagai telah dibaca.
     */
    public function tandaiDibaca(): void
    {
        $this->update([
            'dibaca' => true,
            'dibaca_pada' => now(),
        ]);
    }
}
