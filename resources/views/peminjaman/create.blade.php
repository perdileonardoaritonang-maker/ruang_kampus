@extends('layouts.app')

@section('title', 'Formulir Peminjaman Ruangan - EduSpace')

@section('content')
<div class="max-w-3xl mx-auto my-6">
    <div class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100">
        <!-- Header -->
        <div class="mb-8 border-b border-slate-100 pb-4">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Formulir Peminjaman Ruangan & Lab</h1>
            <p class="text-xs text-slate-500 mt-1">
                Peminjaman disetujui secara otomatis apabila syarat akun terpenuhi dan tidak ada bentrok jadwal kuliah / agenda kampus.
            </p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1">
                <div class="font-bold uppercase tracking-wider text-rose-900 mb-1">Pengajuan Gagal Diproses:</div>
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('peminjaman.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Identitas Peminjam (Otomatis dari Akun Login) -->
            <div class="p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100/70 text-xs text-indigo-900 space-y-1">
                <div class="font-bold uppercase tracking-wider text-indigo-700">Data Peminjam Terhubung:</div>
                <div class="flex flex-wrap gap-x-6 gap-y-1 text-slate-700">
                    <div>Nama: <strong class="text-slate-900">{{ auth()->user()->nama_lengkap }}</strong></div>
                    <div>{{ auth()->user()->jenis_pengguna === 'mahasiswa' ? 'NPM' : 'NIDN' }}: <strong class="text-slate-900 font-mono">{{ auth()->user()->nomor_identitas }}</strong></div>
                    <div>Status: <span class="font-bold text-emerald-600">● Terverifikasi</span></div>
                </div>
            </div>

            <!-- Pilih / Info Ruangan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Ruangan yang Dipilih <span class="text-rose-500">*</span>
                </label>

                @if($selectedRuangan)
                    {{-- Ruangan dikunci: dipilih dari katalog, tidak bisa diubah --}}
                    <input type="hidden" name="ruangan_id" value="{{ $selectedRuangan->id }}">
                    <div class="w-full px-4 py-3 rounded-xl border-2 border-indigo-200 bg-indigo-50/60 text-sm flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-100 text-indigo-700 text-lg font-bold flex-shrink-0">
                                {{ $selectedRuangan->jenis_ruangan === 'laboratorium' ? '🖥' : '🏛' }}
                            </span>
                            <div>
                                <div class="font-bold text-slate-900">{{ $selectedRuangan->nama_ruangan }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    Lantai {{ $selectedRuangan->lantai }} &middot; 
                                    {{ $selectedRuangan->kode_ruangan }} &middot; 
                                    Kapasitas {{ $selectedRuangan->kapasitas }} orang
                                    @if($selectedRuangan->jenis_ruangan === 'laboratorium')
                                        &middot; {{ $selectedRuangan->jumlah_komputer }} PC
                                    @endif
                                </div>
                            </div>
                        </div>
                        <span class="flex-shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold text-emerald-700 bg-emerald-100 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Tersedia
                        </span>
                    </div>
                    <p class="text-[11px] text-indigo-500 mt-1.5 flex items-center gap-1">
                        🔒 Ruangan ini dipilih dari katalog dan tidak dapat diubah. 
                        <a href="{{ route('ruangan.index') }}" class="underline font-semibold hover:text-indigo-700">Ganti ruangan</a>
                    </p>
                @else
                    {{-- Tidak ada ruangan pre-selected: tampilkan dropdown pilihan --}}
                    <select 
                        id="ruangan_id" 
                        name="ruangan_id" 
                        required 
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white"
                    >
                        <option value="" disabled {{ !old('ruangan_id') ? 'selected' : '' }}>Pilih ruangan atau laboratorium dari daftar...</option>
                        @foreach($allRuangan as $r)
                            <option 
                                value="{{ $r->id }}" 
                                {{ old('ruangan_id') == $r->id ? 'selected' : '' }}
                            >
                                [Lantai {{ $r->lantai }}] {{ $r->kode_ruangan }} - {{ $r->nama_ruangan }} (Kapasitas: {{ $r->kapasitas }} org{{ $r->jenis_ruangan === 'laboratorium' ? ', ' . $r->jumlah_komputer . ' PC' : '' }})
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            <!-- Tanggal & Jam -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="tanggal" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tanggal Peminjaman <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="tanggal" 
                        name="tanggal" 
                        value="{{ old('tanggal', $tanggal ?? date('Y-m-d')) }}" 
                        min="{{ date('Y-m-d') }}"
                        required 
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                </div>

                <div>
                    <label for="jam_mulai" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Jam Mulai <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="time" 
                        id="jam_mulai" 
                        name="jam_mulai" 
                        value="{{ old('jam_mulai', $jamMulai ?? '08:00') }}" 
                        required 
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                </div>

                <div>
                    <label for="jam_selesai" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Jam Selesai <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="time" 
                        id="jam_selesai" 
                        name="jam_selesai" 
                        value="{{ old('jam_selesai', $jamSelesai ?? '10:00') }}" 
                        required 
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                </div>
            </div>

            <!-- Jumlah Peserta -->
            <div>
                <label for="jumlah_peserta" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Jumlah Peserta Kegiatan <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="jumlah_peserta" 
                    name="jumlah_peserta" 
                    value="{{ old('jumlah_peserta', 20) }}" 
                    min="1" 
                    required 
                    placeholder="Contoh: 35" 
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                <p class="text-[11px] text-slate-400 mt-1">Pastikan jumlah peserta tidak melebihi batas kapasitas maksimal ruangan terpilih.</p>
            </div>

            <!-- Alasan atau Tujuan Peminjaman (Wajib & Bebas) -->
            <div>
                <label for="alasan_peminjaman" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Alasan / Tujuan Peminjaman (Wajib Diisi Rinci) <span class="text-rose-500">*</span>
                </label>
                <textarea 
                    id="alasan_peminjaman" 
                    name="alasan_peminjaman" 
                    rows="4" 
                    required 
                    placeholder="Jelaskan agenda kegiatan secara spesifik. Contoh: Kegiatan kuliah pengganti mata kuliah Pemrograman Lanjut untuk kelas TI-3A..."
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >{{ old('alasan_peminjaman') }}</textarea>
                <p class="text-[11px] text-slate-400 mt-1">Minimal 10 karakter. Alasan ini akan tercatat resmi pada sistem kampus dan dapat dilihat oleh Biro.</p>
            </div>

            <!-- Catatan Tambahan -->
            <div>
                <label for="catatan_tambahan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Catatan Tambahan (Opsional)
                </label>
                <input 
                    type="text" 
                    id="catatan_tambahan" 
                    name="catatan_tambahan" 
                    value="{{ old('catatan_tambahan') }}" 
                    placeholder="Contoh: Memerlukan kunci lab dibuka 15 menit sebelum acara..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('ruangan.index') }}" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition">
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs shadow-md shadow-indigo-200 transition cursor-pointer"
                >
                    Kirim Permohonan Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
