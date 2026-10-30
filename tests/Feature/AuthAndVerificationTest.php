<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_as_student_with_npm(): void
    {
        $response = $this->post('/register', [
            'nama_lengkap' => 'Budi Pratama',
            'jenis_pengguna' => 'mahasiswa',
            'nomor_identitas' => '2110511099',
            'email' => 'budi@kampus.ac.id',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertRedirect('/identitas/status');
        $this->assertDatabaseHas('users', [
            'email' => 'budi@kampus.ac.id',
            'nomor_identitas' => '2110511099',
            'role' => 'user',
            'status_verifikasi' => 'menunggu',
        ]);
    }

    public function test_user_cannot_register_as_biro_role(): void
    {
        $response = $this->post('/register', [
            'nama_lengkap' => 'Hacker Biro',
            'jenis_pengguna' => 'mahasiswa',
            'nomor_identitas' => '99999999',
            'email' => 'hacker@kampus.ac.id',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role' => 'biro', // Percobaan manipulasi input role
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'hacker@kampus.ac.id',
            'role' => 'user', // Wajib tetap tersimpan sebagai 'user'
        ]);
    }

    public function test_regular_user_cannot_access_biro_routes(): void
    {
        $user = User::factory()->create([
            'nama_lengkap' => 'Regular User',
            'role' => 'user',
            'jenis_pengguna' => 'mahasiswa',
            'nomor_identitas' => '2110001',
            'email' => 'user@kampus.ac.id',
        ]);

        $response = $this->actingAs($user)->get('/biro/verifikasi-pengguna');
        $response->assertStatus(403);
    }

    public function test_biro_can_approve_user_identity(): void
    {
        $biro = User::factory()->create([
            'nama_lengkap' => 'Petugas Biro',
            'role' => 'biro',
            'jenis_pengguna' => 'dosen',
            'nomor_identitas' => 'BIRO-TEST',
            'email' => 'biro@kampus.ac.id',
        ]);

        $student = User::factory()->create([
            'nama_lengkap' => 'Siswa Baru',
            'role' => 'user',
            'jenis_pengguna' => 'mahasiswa',
            'nomor_identitas' => '2110999',
            'email' => 'siswa@kampus.ac.id',
            'status_verifikasi' => 'menunggu',
        ]);

        $response = $this->actingAs($biro)->patch("/biro/verifikasi-pengguna/{$student->id}/approve");
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'status_verifikasi' => 'terverifikasi',
            'diverifikasi_oleh' => $biro->id,
        ]);
    }
}
