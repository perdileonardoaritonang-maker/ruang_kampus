<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Ruangan & Laboratorium
        Schema::create('ruangan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_ruangan', 20)->unique();
            $table->string('nama_ruangan', 100);
            $table->enum('jenis_ruangan', ['ruang_kuliah', 'laboratorium'])->index();
            $table->unsignedTinyInteger('lantai')->index(); // Nomor lantai positif, mendukung hingga 255
            $table->string('lokasi_keterangan', 255)->nullable();
            $table->unsignedSmallInteger('kapasitas');
            $table->unsignedSmallInteger('jumlah_komputer')->default(0);
            $table->string('foto', 255)->nullable();
            $table->enum('status_operasional', ['aktif', 'perawatan', 'nonaktif'])->default('aktif')->index();
            $table->text('keterangan_tambahan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Tabel Master Fasilitas
        Schema::create('fasilitas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_fasilitas', 100)->unique();
            $table->string('icon', 50)->nullable();
            $table->timestamps();
        });

        // 3. Tabel Pivot Fasilitas Ruangan (Many-to-Many)
        Schema::create('fasilitas_ruangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruangan_id')->constrained('ruangan')->cascadeOnDelete();
            $table->foreignId('fasilitas_id')->constrained('fasilitas')->restrictOnDelete();
            $table->unsignedSmallInteger('jumlah')->default(1);
            $table->enum('kondisi', ['baik', 'rusak_sebagian', 'tidak_berfungsi'])->default('baik');
            $table->timestamps();

            $table->unique(['ruangan_id', 'fasilitas_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fasilitas_ruangan');
        Schema::dropIfExists('fasilitas');
        Schema::dropIfExists('ruangan');
    }
};
