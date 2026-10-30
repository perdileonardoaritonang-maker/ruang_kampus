<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $names = [
            'Air Conditioner (AC)',
            'Wi-Fi Kampus',
            'Proyektor & Layar Presentasi',
        ];

        DB::table('fasilitas')->insertOrIgnore(array_map(
            static fn (string $name): array => [
                'nama_fasilitas' => $name,
                'icon' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $names
        ));

        DB::table('fasilitas')
            ->whereNotIn('nama_fasilitas', $names)
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('fasilitas_ruangan')
                    ->whereColumn('fasilitas_ruangan.fasilitas_id', 'fasilitas.id');
            })
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('laporan_kerusakan')
                    ->whereColumn('laporan_kerusakan.fasilitas_id', 'fasilitas.id');
            })
            ->delete();
    }

    public function down(): void
    {
        // Preserve facility records that may be referenced by rooms or reports.
    }
};
