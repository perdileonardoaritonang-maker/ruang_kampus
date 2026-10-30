@extends('layouts.app')

@section('title', 'Detail Laporan ' . $laporan->kode_laporan)

@section('content')
<div class="max-w-3xl mx-auto my-4 space-y-4">
    <!-- Breadcrumb / Back button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('laporan.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Riwayat Laporan
        </a>
        <span class="font-mono text-[11px] text-slate-400">ID: #{{ $laporan->id }}</span>
    </div>

    <!-- Outer Main Card Container -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] overflow-hidden">
        <!-- Card Header Strip -->
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
                <p class="text-[11px] text-slate-400 mt-0.5">Dilaporkan pada {{ $laporan->created_at->format('d M Y, H:i') }} WIB ({{ $laporan->created_at->diffForHumans() }})</p>
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
                        Selesai Diperbaiki
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                        <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        Laporan Ditolak
                    </span>
                @endif
            </div>
        </div>

        <!-- Card Body with Inner Cards -->
        <div class="p-4 sm:p-5 space-y-4">
            <!-- Inner Card 1: Tanggapan Biro -->
            @if($laporan->catatan_tindak_lanjut || $laporan->status !== 'menunggu')
                <div class="rounded-lg p-3.5 border {{ $laporan->status === 'selesai' ? 'bg-emerald-50/60 border-emerald-200/80 text-emerald-950' : ($laporan->status === 'ditolak' ? 'bg-rose-50/60 border-rose-200/80 text-rose-950' : 'bg-blue-50/60 border-blue-200/80 text-blue-950') }} space-y-1.5">
                    <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wider">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            Tanggapan & Tindak Lanjut Biro
                        </span>
                        <span class="font-mono text-[10px] text-slate-500 font-normal">
                            {{ $laporan->updated_at->format('d M Y, H:i') }} WIB
                        </span>
                    </div>
                    <div class="text-xs leading-relaxed font-medium bg-white/80 p-3 rounded-md border border-white/60">
                        {{ $laporan->catatan_tindak_lanjut ?? 'Status laporan telah diperbarui oleh Biro.' }}
                    </div>
                    @if($laporan->penanggungJawab)
                        <div class="text-[10px] text-slate-500 pt-0.5">
                            Ditangani oleh: <strong class="text-slate-700">{{ $laporan->penanggungJawab->nama_lengkap }}</strong> (Biro Pengelola)
                        </div>
                    @endif
                </div>
            @else
                <div class="p-3 rounded-lg bg-amber-50/60 border border-amber-200/70 flex items-center gap-2.5 text-xs text-amber-900">
                    <span class="text-base shrink-0">⏳</span>
                    <div>
                        <strong class="text-[11px]">Laporan dalam antrean tinjauan Biro.</strong>
                        <p class="text-[10px] text-amber-700">Petugas Biro sedang memeriksa laporan ini dan akan memberikan tanggapan tindak lanjut segera.</p>
                    </div>
                </div>
            @endif

            <!-- Inner Card 2: Detail Ruangan & Fasilitas Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <!-- Inner Card: Ruangan -->
                <div class="p-3.5 bg-slate-50/80 rounded-lg border border-slate-200/70 space-y-1">
                    <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block">Lokasi Ruangan</span>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">{{ $laporan->ruangan->nama_ruangan }}</h3>
                    <div class="text-slate-600 text-[11px] flex items-center gap-1.5 flex-wrap">
                        <span class="font-mono font-semibold text-slate-800">{{ $laporan->ruangan->kode_ruangan }}</span>
                        <span>•</span>
                        <span>Lantai {{ $laporan->ruangan->lantai }}</span>
                        <span>•</span>
                        <span>Status: <strong class="capitalize text-slate-800">{{ $laporan->ruangan->status_operasional }}</strong></span>
                    </div>
                </div>

                <!-- Inner Card: Fasilitas -->
                <div class="p-3.5 bg-slate-50/80 rounded-lg border border-slate-200/70 space-y-1">
                    <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block">Fasilitas Terkait</span>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">
                        {{ $laporan->fasilitas ? $laporan->fasilitas->nama_fasilitas : 'Umum / Sarana Ruangan' }}
                    </h3>
                    <div class="text-slate-500 text-[11px]">
                        {{ $laporan->fasilitas ? 'Fasilitas spesifik terdata dalam sistem' : 'Kerusakan mencakup sarana umum ruangan' }}
                    </div>
                </div>
            </div>

            <!-- Inner Card 3: Deskripsi Kendala -->
            <div class="p-3.5 bg-slate-50/80 rounded-lg border border-slate-200/70 space-y-1.5">
                <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block">Deskripsi Kendala / Kerusakan</span>
                <div class="bg-white p-3 rounded-md border border-slate-200/60 text-xs text-slate-800 leading-relaxed whitespace-pre-line">
                    {{ $laporan->deskripsi_masalah }}
                </div>
            </div>

            <!-- Inner Card 4: Foto Bukti Kerusakan (jika ada) -->
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
                                Klik untuk ukuran penuh &nearr;
                            </div>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
