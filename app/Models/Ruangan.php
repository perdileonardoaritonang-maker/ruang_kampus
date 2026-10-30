<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ruangan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ruangan';

    protected $fillable = [
        'kode_ruangan',
        'nama_ruangan',
        'jenis_ruangan',
        'lantai',
        'lokasi_keterangan',
        'kapasitas',
        'jumlah_komputer',
        'foto',
        'status_operasional',
        'keterangan_tambahan',
    ];

    protected function casts(): array
    {
        return [
            'lantai' => 'integer',
            'kapasitas' => 'integer',
            'jumlah_komputer' => 'integer',
        ];
    }

    /**
     * Relasi ke fasilitas yang tersedia di ruangan ini.
     */
    public function fasilitas()
    {
        return $this->belongsToMany(Fasilitas::class, 'fasilitas_ruangan')
                    ->withPivot('id', 'jumlah', 'kondisi')
                    ->withTimestamps();
    }

    /**
     * Relasi ke jadwal tetap kampus di ruangan ini.
     */
    public function jadwalTetap()
    {
        return $this->hasMany(JadwalTetap::class, 'ruangan_id');
    }

    /**
     * Relasi ke seluruh data peminjaman ruangan ini.
     */
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'ruangan_id');
    }

    /**
     * Relasi ke laporan kerusakan pada ruangan ini.
     */
    public function laporanKerusakan()
    {
        return $this->hasMany(LaporanKerusakan::class, 'ruangan_id');
    }

    /**
     * Cek apakah ruangan aktif dan dapat digunakan.
     */
    public function isAvailableForBooking(): bool
    {
        return $this->status_operasional === 'aktif';
    }
}
