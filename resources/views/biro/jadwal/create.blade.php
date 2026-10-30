@extends('layouts.app')

@section('title', 'Tambah Jadwal Tetap Kampus - Biro Pengelola')

@section('content')
<div class="max-w-2xl mx-auto my-6">
    <div class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100">
        <div class="mb-6 border-b border-slate-100 pb-4">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tambah Jadwal Tetap Kampus</h1>
            <p class="text-xs text-slate-500 mt-1">Masukkan jadwal kuliah, praktikum lab, atau jadwal perawatan rutin untuk mengunci slot ruangan.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1">
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('biro.jadwal.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Ruangan -->
            <div>
                <label for="ruangan_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Ruangan <span class="text-rose-500">*</span>
                </label>
                <select id="ruangan_id" name="ruangan_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                    <option value="" disabled selected>Pilih ruangan...</option>
                    @foreach($allRuangan as $r)
                        <option value="{{ $r->id }}">
                            [Lantai {{ $r->lantai }}] {{ $r->kode_ruangan }} - {{ $r->nama_ruangan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Nama Kegiatan -->
            <div>
                <label for="nama_kegiatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nama Mata Kuliah / Agenda Kegiatan <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="nama_kegiatan" 
                    name="nama_kegiatan" 
                    value="{{ old('nama_kegiatan') }}" 
                    required 
                    placeholder="Contoh: Pemrograman Web (Kelas TI-3A) / Praktikum Basis Data" 
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500"
                >
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Jenis Jadwal -->
                <div>
                    <label for="jenis_jadwal" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Jenis Kegiatan <span class="text-rose-500">*</span>
                    </label>
                    <select id="jenis_jadwal" name="jenis_jadwal" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                        <option value="kuliah">Perkuliahan Teori</option>
                        <option value="praktikum">Praktikum Laboratorium</option>
                        <option value="acara_kampus">Acara Resmi Kampus / Seminar</option>
                        <option value="perawatan">Pemeliharaan Fasilitas Ruangan</option>
                    </select>
                </div>

                <!-- Tipe Pengulangan -->
                <div>
                    <label for="tipe_pengulangan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Pola Pengulangan <span class="text-rose-500">*</span>
                    </label>
                    <select id="tipe_pengulangan" name="tipe_pengulangan" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                        <option value="mingguan" selected>Mingguan (Rutin Tiap Minggu)</option>
                        <option value="tidak_berulang">Tanggal Tunggal Tertentu</option>
                    </select>
                </div>
            </div>

            <!-- Hari (Jika Mingguan) -->
            <div id="hari_container">
                <label for="hari" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Hari Perkuliahan <span class="text-rose-500">*</span>
                </label>
                <select id="hari" name="hari" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                    <option value="senin">Senin</option>
                    <option value="selasa">Selasa</option>
                    <option value="rabu">Rabu</option>
                    <option value="kamis">Kamis</option>
                    <option value="jumat">Jumat</option>
                    <option value="sabtu">Sabtu</option>
                </select>
            </div>

            <!-- Tanggal Mulai & Selesai Efektif Semester -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="tanggal_mulai_efektif" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tanggal Mulai Berlaku <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="tanggal_mulai_efektif" name="tanggal_mulai_efektif" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm">
                </div>
                <div>
                    <label for="tanggal_selesai_efektif" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tanggal Selesai Berlaku (Akhir Semester) <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="tanggal_selesai_efektif" name="tanggal_selesai_efektif" value="{{ date('Y-m-d', strtotime('+6 months')) }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm">
                </div>
            </div>

            <!-- Jam Mulai & Selesai -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="jam_mulai" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Jam Mulai <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" id="jam_mulai" name="jam_mulai" value="08:00" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm">
                </div>
                <div>
                    <label for="jam_selesai" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Jam Selesai <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" id="jam_selesai" name="jam_selesai" value="10:00" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm">
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('biro.jadwal.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition">
                    Batal
                </a>
                <button type="submit" class="px-7 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs shadow-md shadow-indigo-200 transition cursor-pointer">
                    Simpan Jadwal Tetap
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
