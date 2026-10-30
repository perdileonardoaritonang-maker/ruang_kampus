@extends('layouts.app')

@section('title', 'Buat Laporan Kerusakan Fasilitas - EduSpace')

@section('content')
<div class="max-w-2xl mx-auto my-6">
    <div class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100">
        <div class="mb-6 border-b border-slate-100 pb-4">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Laporkan Kendala Fasilitas</h1>
            <p class="text-xs text-slate-500 mt-1">Laporan Anda akan langsung masuk ke antrean pemeliharaan sarana oleh Biro Kampus.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1">
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Ruangan -->
            <div>
                <label for="ruangan_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Ruangan Terkait <span class="text-rose-500">*</span>
                </label>
                <select id="ruangan_id" name="ruangan_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                    <option value="" disabled {{ !$selectedRuanganId ? 'selected' : '' }}>Pilih ruangan lokasi kerusakan...</option>
                    @foreach($allRuangan as $r)
                        <option value="{{ $r->id }}" {{ (string)$selectedRuanganId === (string)$r->id ? 'selected' : '' }}>
                            [Lantai {{ $r->lantai }}] {{ $r->kode_ruangan }} - {{ $r->nama_ruangan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Fasilitas Terkait -->
            <div>
                <label for="fasilitas_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Fasilitas Terkait (Jika Diketahui)
                </label>
                <select id="fasilitas_id" name="fasilitas_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                    <option value="">-- Pilih Fasilitas (Opsional) --</option>
                    @foreach($allFasilitas as $f)
                        <option value="{{ $f->id }}">{{ $f->nama_fasilitas }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Judul Laporan -->
            <div>
                <label for="judul_laporan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Judul Singkat Kerusakan <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="judul_laporan" 
                    name="judul_laporan" 
                    value="{{ old('judul_laporan') }}" 
                    required 
                    placeholder="Contoh: AC tidak dingin / Proyektor berkedip mati / Kursi patah" 
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-rose-500"
                >
            </div>

            <!-- Deskripsi Masalah Bebas -->
            <div>
                <label for="deskripsi_masalah" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Deskripsi Lengkap Masalah <span class="text-rose-500">*</span>
                </label>
                <textarea 
                    id="deskripsi_masalah" 
                    name="deskripsi_masalah" 
                    rows="4" 
                    required 
                    placeholder="Jelaskan kondisi detail kejadian. Contoh: Pada baris tengah meja 3, layar proyektor buram dan bergetar saat disambungkan kabel HDMI..." 
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-rose-500"
                >{{ old('deskripsi_masalah') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Tingkat Urgensi -->
                <div>
                    <label for="tingkat_urgensi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Tingkat Urgensi <span class="text-rose-500">*</span>
                    </label>
                    <select id="tingkat_urgensi" name="tingkat_urgensi" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                        <option value="rendah">Rendah (Masih bisa digunakan)</option>
                        <option value="sedang" selected>Sedang (Mengganggu perkuliahan)</option>
                        <option value="tinggi">Tinggi (Tidak berfungsi sama sekali)</option>
                        <option value="darurat">Darurat (Berisiko membahayakan)</option>
                    </select>
                </div>

                <!-- Foto Pendukung -->
                <div>
                    <label for="foto" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Foto Bukti Kerusakan (Opsional)
                    </label>
                    <input 
                        type="file" 
                        id="foto" 
                        name="foto" 
                        accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200"
                    >
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('laporan.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition">
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="px-7 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs shadow-md shadow-rose-200 transition cursor-pointer"
                >
                    Kirim Laporan Kerusakan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
