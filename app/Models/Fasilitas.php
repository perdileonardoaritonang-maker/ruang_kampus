<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    use HasFactory;

    protected $table = 'fasilitas';

    protected $fillable = [
        'nama_fasilitas',
        'icon',
    ];

    /**
     * Relasi ke ruangan yang memiliki fasilitas ini.
     */
    public function ruangan()
    {
        return $this->belongsToMany(Ruangan::class, 'fasilitas_ruangan')
                    ->withPivot('id', 'jumlah', 'kondisi')
                    ->withTimestamps();
    }
}
