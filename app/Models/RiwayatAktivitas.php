<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatAktivitas extends Model
{
    use HasFactory;

    protected $table = 'riwayat_aktivitas';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'jenis_aktivitas',
        'deskripsi',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Catat log aktivitas secara cepat.
     */
    public static function catat(string $jenis, string $deskripsi, ?int $userId = null): void
    {
        static::create([
            'user_id' => $userId ?? auth()->id(),
            'jenis_aktivitas' => $jenis,
            'deskripsi' => $deskripsi,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
