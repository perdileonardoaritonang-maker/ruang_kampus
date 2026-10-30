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
        // 4. Tabel Jadwal Tetap Kampus (Kuliah, Praktikum, Seminar, Perawatan Rutin)
        Schema::create('jadwal_tetap', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruangan_id')->constrained('ruangan')->restrictOnDelete();
            $table->string('nama_kegiatan', 150);
            $table->enum('jenis_jadwal', ['kuliah', 'praktikum', 'acara_kampus', 'perawatan', 'penutupan_sementara']);
            $table->enum('tipe_pengulangan', ['tidak_berulang', 'mingguan'])->default('mingguan');
            $table->enum('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'])->nullable();
            $table->date('tanggal_spesifik')->nullable();
            $table->date('tanggal_mulai_efektif');
            $table->date('tanggal_selesai_efektif');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['ruangan_id', 'hari', 'jam_mulai', 'jam_selesai'], 'idx_jadwal_rutin_waktu');
        });

        // 5. Tabel Peminjaman Ruangan
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('kode_booking', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('ruangan_id')->constrained('ruangan')->restrictOnDelete();
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->unsignedSmallInteger('jumlah_peserta');
            $table->text('alasan_peminjaman');
            $table->text('catatan_tambahan')->nullable();
            $table->enum('status', ['disetujui', 'selesai', 'dibatalkan_user', 'dibatalkan_biro'])->default('disetujui')->index();
            $table->text('alasan_pembatalan')->nullable();
            $table->foreignId('dibatalkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dibatalkan_pada')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status'], 'idx_peminjaman_user_status');
            $table->index(['ruangan_id', 'tanggal', 'jam_mulai', 'jam_selesai', 'status'], 'idx_peminjaman_slot_waktu');
        });

        // 6. Tabel Laporan Kerusakan Fasilitas
        Schema::create('laporan_kerusakan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_laporan', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('ruangan_id')->constrained('ruangan')->restrictOnDelete();
            $table->foreignId('fasilitas_id')->nullable()->constrained('fasilitas')->nullOnDelete();
            $table->string('judul_laporan', 150);
            $table->text('deskripsi_masalah');
            $table->enum('tingkat_urgensi', ['rendah', 'sedang', 'tinggi', 'darurat'])->default('sedang');
            $table->string('foto', 255)->nullable();
            $table->enum('status', ['menunggu', 'sedang_ditangani', 'selesai', 'ditolak'])->default('menunggu')->index();
            $table->text('catatan_tindak_lanjut')->nullable();
            $table->foreignId('ditangani_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('selesai_pada')->nullable();
            $table->timestamps();
        });

        // 7. Tabel Riwayat Aktivitas (Audit Trail)
        Schema::create('riwayat_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('jenis_aktivitas', 50)->index();
            $table->text('deskripsi');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_aktivitas');
        Schema::dropIfExists('laporan_kerusakan');
        Schema::dropIfExists('peminjaman');
        Schema::dropIfExists('jadwal_tetap');
    }
};
