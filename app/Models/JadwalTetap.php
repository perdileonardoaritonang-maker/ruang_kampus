<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalTetap extends Model
{
    use HasFactory;

    protected $table = 'jadwal_tetap';

    protected $fillable = [
        'ruangan_id',
        'nama_kegiatan',
        'jenis_jadwal',
        'tipe_pengulangan',
        'hari',
        'tanggal_spesifik',
        'tanggal_mulai_efektif',
        'tanggal_selesai_efektif',
        'jam_mulai',
        'jam_selesai',
        'keterangan',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_spesifik' => 'date',
            'tanggal_mulai_efektif' => 'date',
            'tanggal_selesai_efektif' => 'date',
        ];
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
