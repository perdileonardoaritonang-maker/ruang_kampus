<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKerusakan extends Model
{
    use HasFactory;

    protected $table = 'laporan_kerusakan';

    protected $fillable = [
        'kode_laporan',
        'user_id',
        'ruangan_id',
        'fasilitas_id',
        'judul_laporan',
        'deskripsi_masalah',
        'tingkat_urgensi',
        'foto',
        'status',
        'catatan_tindak_lanjut',
        'ditangani_oleh',
        'selesai_pada',
    ];

    protected function casts(): array
    {
        return [
            'selesai_pada' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function fasilitas()
    {
        return $this->belongsTo(Fasilitas::class, 'fasilitas_id');
    }

    public function penanggungJawab()
    {
        return $this->belongsTo(User::class, 'ditangani_oleh');
    }
}
