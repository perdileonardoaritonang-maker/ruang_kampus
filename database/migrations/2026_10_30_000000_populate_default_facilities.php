<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Populate the shared master list used by room create/edit forms and catalog details.
     * Existing facility records are preserved; unique names are inserted only once.
     */
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
    }

    /**
     * Keep facility master data on rollback because rooms or reports may reference it.
     */
    public function down(): void
    {
        // Intentionally does not delete shared facility records.
    }
};
