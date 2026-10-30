<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /** Seed akun contoh yang digunakan selama pengembangan. */
    public function run(): void
    {
        // Role dan status_verifikasi tidak ada di $fillable model User.
        // unguarded hanya berlaku selama seeder ini berjalan.
        User::unguarded(function (): void {
            User::firstOrCreate(
                ['email' => 'biro@kampus.ac.id'],
                [
                    'nama_lengkap' => 'Biro Administrasi & Sarana Kampus',
                    'password' => Hash::make('password123'),
                    'role' => 'biro',
                    'jenis_pengguna' => 'dosen',
                    'nomor_identitas' => 'BIRO-001',
                    'status_verifikasi' => 'terverifikasi',
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );

            User::firstOrCreate(
                ['email' => 'mahasiswa@kampus.ac.id'],
                [
                    'nama_lengkap' => 'Ahmad Fajar Prasetyo',
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'jenis_pengguna' => 'mahasiswa',
                    'nomor_identitas' => '2110511045',
                    'status_verifikasi' => 'terverifikasi',
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );

            User::firstOrCreate(
                ['email' => 'siti.nurhaliza@kampus.ac.id'],
                [
                    'nama_lengkap' => 'Siti Nurhaliza',
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'jenis_pengguna' => 'mahasiswa',
                    'nomor_identitas' => '2210512019',
                    'status_verifikasi' => 'menunggu',
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );

            User::firstOrCreate(
                ['email' => 'dosen@kampus.ac.id'],
                [
                    'nama_lengkap' => 'Dr. Ir. Hendra Wijaya, M.Kom.',
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'jenis_pengguna' => 'dosen',
                    'nomor_identitas' => '0412088201',
                    'status_verifikasi' => 'terverifikasi',
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );

            User::firstOrCreate(
                ['email' => 'budi.ditolak@kampus.ac.id'],
                [
                    'nama_lengkap' => 'Budi Santoso',
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'jenis_pengguna' => 'mahasiswa',
                    'nomor_identitas' => '2010511002',
                    'status_verifikasi' => 'ditolak',
                    'alasan_penolakan' => 'Foto kartu identitas (KTM) buram dan masa berlaku habis.',
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );
        });
    }
}
