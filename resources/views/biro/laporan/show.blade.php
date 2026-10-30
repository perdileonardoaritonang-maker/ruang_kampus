@extends('layouts.app')

@section('title', 'Detail Laporan ' . $laporan->kode_laporan . ' - Biro Pengelola')

@section('content')
<div class="max-w-3xl mx-auto my-4 space-y-4">
    <!-- Breadcrumb -->
    <div class="flex items-center justify-between">
        <a href="{{ route('biro.laporan.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Penanganan Laporan
        </a>
        <span class="font-mono text-[11px] text-slate-400">ID: #{{ $laporan->id }}</span>
    </div>

    <!-- Outer Main Card Container -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] overflow-hidden">
        <!-- Header Strip -->
        <div class="px-5 py-3.5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 bg-slate-50/40">
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-mono text-xs font-bold text-slate-900 bg-white px-2 py-0.5 rounded border border-slate-200">
                        {{ $laporan->kode_laporan }}
                    </span>
                    @if($laporan->tingkat_urgensi === 'darurat')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-rose-50 text-rose-700 border border-rose-200">
                            ● Darurat
                        </span>
                    @elseif($laporan->tingkat_urgensi === 'tinggi')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">
                            ● Tinggi
                        </span>
                    @elseif($laporan->tingkat_urgensi === 'sedang')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-blue-50 text-blue-700 border border-blue-200">
                            Sedang
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-slate-100 text-slate-600 border border-slate-200">
                            Rendah
                        </span>
                    @endif
                </div>
                <h1 class="text-base font-bold text-slate-900 tracking-tight mt-1">{{ $laporan->judul_laporan }}</h1>
                <p class="text-[11px] text-slate-400 mt-0.5">Diajukan pada {{ $laporan->created_at->format('d M Y, H:i') }} WIB ({{ $laporan->created_at->diffForHumans() }})</p>
            </div>

            <div class="shrink-0">
                @if($laporan->status === 'menunggu')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        Menunggu Tinjauan
                    </span>
                @elseif($laporan->status === 'sedang_ditangani')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        Sedang Ditangani
                    </span>
                @elseif($laporan->status === 'selesai')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Selesai
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                        <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        Ditolak
                    </span>
                @endif
            </div>
        </div>

        <!-- Body with Inner Cards -->
        <div class="p-4 sm:p-5 space-y-4">
            <!-- Inner Card 1: Grid Pelapor & Ruangan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <!-- Inner Card: Pelapor -->
                <div class="p-3.5 bg-slate-50/80 rounded-lg border border-slate-200/70 space-y-1">
                    <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block">Identitas Pelapor</span>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">{{ $laporan->user->nama_lengkap }}</h3>
                    <div class="text-slate-600 text-[11px]">
                        Peran: <strong class="uppercase text-slate-700">{{ $laporan->user->jenis_pengguna }}</strong>
                        • No. Identitas: <span class="font-mono text-slate-700 font-semibold">{{ $laporan->user->nomor_identitas }}</span>
                    </div>
                    <div class="text-[11px] text-slate-400">{{ $laporan->user->email }}</div>
                </div>

                <!-- Inner Card: Lokasi Ruangan & Fasilitas -->
                <div class="p-3.5 bg-slate-50/80 rounded-lg border border-slate-200/70 space-y-1">
                    <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block">Lokasi & Fasilitas</span>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">{{ $laporan->ruangan->nama_ruangan }} ({{ $laporan->ruangan->kode_ruangan }})</h3>
                    <div class="text-slate-600 text-[11px]">
                        Lantai {{ $laporan->ruangan->lantai }} • Status: <strong class="capitalize text-slate-800">{{ $laporan->ruangan->status_operasional }}</strong>
                    </div>
                    <div class="text-[11px] text-slate-500">
                        Fasilitas: <strong>{{ $laporan->fasilitas ? $laporan->fasilitas->nama_fasilitas : 'Umum / Sarana Ruangan' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Inner Card 2: Deskripsi Masalah -->
            <div class="p-3.5 bg-slate-50/80 rounded-lg border border-slate-200/70 space-y-1.5">
                <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block">Deskripsi Masalah</span>
                <div class="bg-white p-3 rounded-md border border-slate-200/60 text-xs text-slate-800 leading-relaxed whitespace-pre-line">
                    {{ $laporan->deskripsi_masalah }}
                </div>
            </div>

            <!-- Inner Card 3: Foto Bukti Kerusakan -->
            @if($laporan->foto)
                <div class="p-3.5 bg-slate-50/80 rounded-lg border border-slate-200/70 space-y-2">
                    <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block">Foto Bukti Kerusakan</span>
                    <div class="bg-white p-2.5 rounded-md border border-slate-200/60 inline-block max-w-full">
                        <a href="{{ route('laporan.foto', $laporan) }}" target="_blank" rel="noopener noreferrer" class="block group relative overflow-hidden rounded">
                            <img 
                                src="{{ route('laporan.foto', $laporan) }}" 
                                alt="Bukti Kerusakan" 
                                class="max-h-60 w-auto rounded object-contain group-hover:opacity-95 transition"
                            />
                            <div class="absolute inset-0 bg-slate-900/20 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-[11px] font-semibold">
                                Buka ukuran penuh &nearr;
                            </div>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Inner Card 4: Form Tindak Lanjut Biro -->
            <div class="p-4 bg-slate-50/80 rounded-lg border border-slate-200/80 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-200/70 pb-2">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Pembaruan & Tindak Lanjut Biro
                    </h3>
                    <span class="text-[10px] text-slate-400">Notifikasi otomatis terkirim ke pelapor</span>
                </div>

                <form action="{{ route('biro.laporan.update-status', $laporan) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="status" class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">
                                Status Laporan <span class="text-rose-500">*</span>
                            </label>
                            <select id="status" name="status" required class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:outline-none focus:ring-1 focus:ring-slate-900">
                                <option value="menunggu" {{ $laporan->status === 'menunggu' ? 'selected' : '' }}>Menunggu Tinjauan</option>
                                <option value="sedang_ditangani" {{ $laporan->status === 'sedang_ditangani' ? 'selected' : '' }}>Sedang Ditangani (Teknisi Dikirim)</option>
                                <option value="selesai" {{ $laporan->status === 'selesai' ? 'selected' : '' }}>Selesai (Sudah Diperbaiki)</option>
                                <option value="ditolak" {{ $laporan->status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        <div>
                            <label for="ubah_status_ruangan" class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">
                                Operasional Ruangan (Opsional)
                            </label>
                            <select id="ubah_status_ruangan" name="ubah_status_ruangan" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:outline-none focus:ring-1 focus:ring-slate-900">
                                <option value="">-- Pertahankan Status ({{ ucfirst($laporan->ruangan->status_operasional) }}) --</option>
                                <option value="perawatan">Tutup Sementara (Perawatan)</option>
                                <option value="aktif">Buka Kembali (Aktif)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="catatan_tindak_lanjut" class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">
                            Catatan Tindak Lanjut untuk Pelapor
                        </label>
                        <textarea 
                            name="catatan_tindak_lanjut" 
                            id="catatan_tindak_lanjut" 
                            rows="2" 
                            placeholder="Tuliskan catatan tindak lanjut yang akan dikirimkan ke pelapor..." 
                            class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs focus:ring-1 focus:ring-slate-900 focus:outline-none bg-white"
                        >{{ old('catatan_tindak_lanjut', $laporan->catatan_tindak_lanjut) }}</textarea>
                    </div>

                    @if($laporan->penanggungJawab)
                        <div class="text-[10px] text-slate-400">
                            Terakhir ditangani oleh: <strong class="text-slate-600">{{ $laporan->penanggungJawab->nama_lengkap }}</strong> pada {{ $laporan->updated_at->format('d M Y, H:i') }} WIB
                        </div>
                    @endif

                    <div class="flex items-center justify-end gap-2 pt-1">
                        <button type="submit" class="px-4 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-lg text-xs transition cursor-pointer shadow-xs">
                            Simpan Tindak Lanjut
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
