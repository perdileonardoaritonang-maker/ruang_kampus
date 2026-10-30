@extends('layouts.app')

@section('title', 'Daftar Akun Baru - EduSpace')

@section('content')
<div class="max-w-lg mx-auto my-8">
    <div class="bg-white p-8 rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pendaftaran Akun Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar sebagai Mahasiswa atau Dosen untuk meminjam ruangan & lab</p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium space-y-1">
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Nama Lengkap -->
            <div>
                <label for="nama_lengkap" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                <input 
                    type="text" 
                    id="nama_lengkap" 
                    name="nama_lengkap" 
                    value="{{ old('nama_lengkap') }}" 
                    required 
                    placeholder="Contoh: Budi Santoso" 
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition"
                >
            </div>

            <!-- Jenis Pengguna -->
            <div>
                <label for="jenis_pengguna" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Pengguna</label>
                <select 
                    id="jenis_pengguna" 
                    name="jenis_pengguna" 
                    required 
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-white transition"
                >
                    <option value="" disabled {{ old('jenis_pengguna') ? '' : 'selected' }}>Pilih jenis pengguna...</option>
                    <option value="mahasiswa" {{ old('jenis_pengguna') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa (Gunakan NPM)</option>
                    <option value="dosen" {{ old('jenis_pengguna') === 'dosen' ? 'selected' : '' }}>Dosen (Gunakan NIDN / NIP)</option>
                </select>
            </div>

            <!-- Nomor Identitas -->
            <div>
                <label for="nomor_identitas" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nomor Identitas Kampus (<span id="id_label_hint">NPM / NIDN / NIP</span>)
                </label>
                <input 
                    type="text" 
                    id="nomor_identitas" 
                    name="nomor_identitas" 
                    value="{{ old('nomor_identitas') }}" 
                    required 
                    placeholder="Contoh: 2110511001" 
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition"
                >
                <p class="text-[11px] text-slate-400 mt-1">Pastikan sesuai dengan kartu tanda mahasiswa (KTM) atau SK dosen resmi.</p>
            </div>

            <!-- Email Kampus -->
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    placeholder="email.aktif@kampus.ac.id" 
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition"
                >
            </div>

            <!-- Grid Sandi & Konfirmasi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        placeholder="Min 8 karakter" 
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition"
                    >
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Konfirmasi Sandi</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        required 
                        placeholder="Ketik ulang sandi" 
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition"
                    >
                </div>
            </div>

            <div class="pt-2">
                <button 
                    type="submit" 
                    class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-md shadow-indigo-200 transition text-sm cursor-pointer"
                >
                    Daftar Akun Sekarang
                </button>
            </div>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
            Sudah memiliki akun? 
            <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-700">Masuk di sini</a>
        </div>
    </div>
</div>

<script>
    // Dinamis ubah label petunjuk nomor identitas
    const roleSelect = document.getElementById('jenis_pengguna');
    const idHint = document.getElementById('id_label_hint');
    const idInput = document.getElementById('nomor_identitas');

    roleSelect.addEventListener('change', function() {
        if (this.value === 'mahasiswa') {
            idHint.textContent = 'NPM Mahasiswa';
            idInput.placeholder = 'Contoh: 2110511001';
        } else if (this.value === 'dosen') {
            idHint.textContent = 'NIDN / NIP Dosen';
            idInput.placeholder = 'Contoh: 0412088501';
        } else {
            idHint.textContent = 'NPM / NIDN / NIP';
            idInput.placeholder = 'Contoh: Nomor Identitas Resmi';
        }
    });
</script>
@endsection
