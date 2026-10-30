<?php

namespace Tests\Feature;

use App\Models\JadwalTetap;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BookingFlowAndCollisionTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;

    protected User $biro;

    protected Ruangan $ruangan;

    protected BookingService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(BookingService::class);

        // User Mahasiswa Terverifikasi
        $this->student = User::factory()->create([
            'nama_lengkap' => 'Mahasiswa Uji',
            'email' => 'student.test@kampus.ac.id',
            'role' => 'user',
            'jenis_pengguna' => 'mahasiswa',
            'nomor_identitas' => '21105999',
            'status_verifikasi' => 'terverifikasi',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Akun Biro
        $this->biro = User::factory()->create([
            'nama_lengkap' => 'Staf Biro Uji',
            'email' => 'biro.test@kampus.ac.id',
            'role' => 'biro',
            'jenis_pengguna' => 'dosen',
            'nomor_identitas' => 'BIRO-TEST',
            'status_verifikasi' => 'terverifikasi',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Ruangan Uji Lantai 3
        $this->ruangan = Ruangan::create([
            'kode_ruangan' => '303-TEST',
            'nama_ruangan' => 'Ruang Kuliah Uji 303',
            'jenis_ruangan' => 'ruang_kuliah',
            'lantai' => 3,
            'kapasitas' => 50,
            'status_operasional' => 'aktif',
        ]);
    }

    public function test_verified_user_can_book_room_and_auto_approved(): void
    {
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        $peminjaman = $this->service->createBooking($this->student, [
            'ruangan_id' => $this->ruangan->id,
            'tanggal' => $tomorrow,
            'jam_mulai' => '13:00',
            'jam_selesai' => '15:00',
            'jumlah_peserta' => 30,
            'alasan_peminjaman' => 'Diskusi kelompok mata kuliah RPL untuk tugas akhir',
        ]);

        $this->assertNotNull($peminjaman->id);
        $this->assertEquals('disetujui', $peminjaman->status);
        $this->assertDatabaseHas('peminjaman', [
            'id' => $peminjaman->id,
            'user_id' => $this->student->id,
            'ruangan_id' => $this->ruangan->id,
            'status' => 'disetujui',
        ]);
    }

    public function test_cannot_book_overlapping_time_with_another_booking(): void
    {
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        // Booking 1: 13:00 - 15:00
        $this->service->createBooking($this->student, [
            'ruangan_id' => $this->ruangan->id,
            'tanggal' => $tomorrow,
            'jam_mulai' => '13:00',
            'jam_selesai' => '15:00',
            'jumlah_peserta' => 30,
            'alasan_peminjaman' => 'Kegiatan pertama kelompok belajar',
        ]);

        // Mahasiswa lain coba booking di ruangan yang sama pada jam bertabrakan (14:00 - 16:00)
        $student2 = User::factory()->create([
            'nama_lengkap' => 'Mahasiswa Uji 2',
            'email' => 'student2@kampus.ac.id',
            'role' => 'user',
            'jenis_pengguna' => 'mahasiswa',
            'nomor_identitas' => '21105888',
            'status_verifikasi' => 'terverifikasi',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->expectException(ValidationException::class);

        $this->service->createBooking($student2, [
            'ruangan_id' => $this->ruangan->id,
            'tanggal' => $tomorrow,
            'jam_mulai' => '14:00',
            'jam_selesai' => '16:00',
            'jumlah_peserta' => 25,
            'alasan_peminjaman' => 'Kegiatan kedua yang harus ditolak bentrok',
        ]);
    }

    public function test_can_book_adjacent_time_without_collision(): void
    {
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        // Booking 1: 08:00 - 10:00
        $this->service->createBooking($this->student, [
            'ruangan_id' => $this->ruangan->id,
            'tanggal' => $tomorrow,
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'jumlah_peserta' => 30,
            'alasan_peminjaman' => 'Kuliah pengganti pagi hari',
        ]);

        $student2 = User::factory()->create([
            'nama_lengkap' => 'Mahasiswa Uji 2',
            'email' => 'student2@kampus.ac.id',
            'role' => 'user',
            'jenis_pengguna' => 'mahasiswa',
            'nomor_identitas' => '21105888',
            'status_verifikasi' => 'terverifikasi',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Booking 2 tepat dimulai pukul 10:00 - 12:00 (Adjacent / slot berikutnya) -> Harus berhasil!
        $booking2 = $this->service->createBooking($student2, [
            'ruangan_id' => $this->ruangan->id,
            'tanggal' => $tomorrow,
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'jumlah_peserta' => 20,
            'alasan_peminjaman' => 'Seminar kelompok siang hari tepat setelah jam 10',
        ]);

        $this->assertEquals('disetujui', $booking2->status);
    }

    public function test_cannot_have_more_than_one_active_booking(): void
    {
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        // Booking Pertama aktif: 08:00 - 10:00
        $this->service->createBooking($this->student, [
            'ruangan_id' => $this->ruangan->id,
            'tanggal' => $tomorrow,
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'jumlah_peserta' => 30,
            'alasan_peminjaman' => 'Peminjaman pertama yang masih aktif',
        ]);

        // Mencoba mengajukan peminjaman kedua di waktu bentrok pada ruangan lain -> Harus ditolak!
        $this->expectException(ValidationException::class);

        $ruangan2 = Ruangan::create([
            'kode_ruangan' => '304-TEST',
            'nama_ruangan' => 'Ruang Uji 304',
            'lantai' => 3,
            'jenis_ruangan' => 'ruang_kuliah',
            'kapasitas' => 50,
            'status_operasional' => 'aktif',
        ]);

        $this->service->createBooking($this->student, [
            'ruangan_id' => $ruangan2->id,
            'tanggal' => $tomorrow,
            'jam_mulai' => '08:30',
            'jam_selesai' => '09:30',
            'jumlah_peserta' => 20,
            'alasan_peminjaman' => 'Percobaan peminjaman kedua pada jam bentrok',
        ]);
    }

    public function test_cannot_book_when_colliding_with_routine_lecture_schedule(): void
    {
        // Pasang jadwal kuliah rutin setiap Senin pukul 08:00 - 10:00
        JadwalTetap::create([
            'ruangan_id' => $this->ruangan->id,
            'nama_kegiatan' => 'Kuliah Pemrograman Web Rutin',
            'jenis_jadwal' => 'kuliah',
            'tipe_pengulangan' => 'mingguan',
            'hari' => 'senin',
            'tanggal_mulai_efektif' => '2026-01-01',
            'tanggal_selesai_efektif' => '2027-12-31',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'created_by' => $this->biro->id,
        ]);

        // Cari tanggal hari Senin berikutnya
        $nextMonday = date('Y-m-d', strtotime('next monday'));

        $this->expectException(ValidationException::class);

        // Mahasiswa mencoba meminjam di hari Senin jam 09:00 - 11:00 (Bentrok dengan kuliah rutin)
        $this->service->createBooking($this->student, [
            'ruangan_id' => $this->ruangan->id,
            'tanggal' => $nextMonday,
            'jam_mulai' => '09:00',
            'jam_selesai' => '11:00',
            'jumlah_peserta' => 20,
            'alasan_peminjaman' => 'Pengajuan yang bertabrakan dengan jadwal kuliah',
        ]);
    }

    public function test_biro_can_view_active_and_expired_bookings_in_separate_tabs(): void
    {
        $this->createAdminBooking('BOOK-ACTIVE-TEST', now()->addDay()->toDateString(), 'disetujui');
        $this->createAdminBooking('BOOK-EXPIRED-TEST', now()->subDay()->toDateString(), 'selesai');

        $activeResponse = $this->actingAs($this->biro)->get(route('biro.peminjaman.index'));
        $activeResponse->assertOk()
            ->assertSee('Aktif / Sekarang')
            ->assertSee('BOOK-ACTIVE-TEST')
            ->assertDontSee('BOOK-EXPIRED-TEST');

        $expiredResponse = $this->actingAs($this->biro)->get(route('biro.peminjaman.index', [
            'tab' => 'kedaluwarsa',
        ]));
        $expiredResponse->assertOk()
            ->assertSee('Kedaluwarsa')
            ->assertSee('BOOK-EXPIRED-TEST')
            ->assertDontSee('BOOK-ACTIVE-TEST');
    }

    public function test_biro_can_still_filter_cancelled_bookings_from_either_tab(): void
    {
        $this->createAdminBooking('BOOK-CANCELLED-TEST', now()->addDay()->toDateString(), 'dibatalkan_user');

        $response = $this->actingAs($this->biro)->get(route('biro.peminjaman.index', [
            'tab' => 'aktif',
            'status' => 'dibatalkan_user',
        ]));

        $response->assertOk()->assertSee('BOOK-CANCELLED-TEST');
    }

    private function createAdminBooking(string $code, string $date, string $status): Peminjaman
    {
        return Peminjaman::create([
            'kode_booking' => $code,
            'user_id' => $this->student->id,
            'ruangan_id' => $this->ruangan->id,
            'tanggal' => $date,
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
            'jumlah_peserta' => 10,
            'alasan_peminjaman' => 'Kegiatan mahasiswa untuk pengujian.',
            'status' => $status,
        ]);
    }
}
