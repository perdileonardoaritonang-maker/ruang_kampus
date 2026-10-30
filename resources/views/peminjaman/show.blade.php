@extends('layouts.app')

@section('title', 'Detail Peminjaman ' . $peminjaman->kode_booking)

@section('content')
<div class="max-w-3xl mx-auto my-4 space-y-4">
    <!-- Breadcrumb -->
    <div class="flex items-center justify-between">
        <a href="{{ route('peminjaman.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Riwayat Peminjaman
        </a>
        <span class="font-mono text-[11px] text-slate-400">ID: #{{ $peminjaman->id }}</span>
    </div>

    <!-- Outer Main Card Container -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] overflow-hidden">
        <!-- Card Header Strip -->
        <div class="px-5 py-3.5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 bg-slate-50/40">
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-mono text-xs font-bold text-slate-900 bg-white px-2 py-0.5 rounded border border-slate-200">
                        {{ $peminjaman->kode_booking }}
                    </span>
                    <span class="text-[11px] text-slate-400 font-medium">Diajukan: {{ $peminjaman->created_at->format('d M Y, H:i') }} WIB</span>
                </div>
                <h1 class="text-base font-bold text-slate-900 tracking-tight mt-1">Detail Peminjaman Ruangan</h1>
            </div>

            <div class="shrink-0">
                @if($peminjaman->status === 'disetujui' && !$peminjaman->isExpired())
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Disetujui Otomatis
                    </span>
                @elseif($peminjaman->status === 'selesai' || $peminjaman->isExpired())
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                        ● Kedaluwarsa
                    </span>
                @elseif($peminjaman->status === 'dibatalkan_user')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                        Dibatalkan Peminjam
                    </span>
                @elseif($peminjaman->status === 'dibatalkan_biro')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                        Dibatalkan oleh Biro
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                        ● Kedaluwarsa
                    </span>
                @endif
            </div>
        </div>

        <!-- Card Body with Inner Cards -->
        <div class="p-4 sm:p-5 space-y-4">
            <!-- Inner Card 1: Jadwal & Waktu Pemakaian Ringkas -->
            <div class="p-3.5 bg-slate-50/80 rounded-lg border border-slate-200/70 flex flex-wrap justify-between items-center gap-3 text-xs">
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Tanggal Kegiatan</span>
                    <span class="text-sm font-bold text-slate-900">{{ $peminjaman->tanggal->format('l, d F Y') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Waktu Pemakaian</span>
                    <span class="text-sm font-bold text-slate-900 font-mono">{{ substr($peminjaman->jam_mulai, 0, 5) }} - {{ substr($peminjaman->jam_selesai, 0, 5) }} WIB</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Peserta</span>
                    <span class="text-sm font-bold text-slate-900">{{ $peminjaman->jumlah_peserta }} Orang</span>
                </div>
            </div>

            <!-- Inner Card 2: Grid Ruangan & Peminjam -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <!-- Inner Card: Ruangan -->
                <div class="p-3.5 bg-slate-50/80 rounded-lg border border-slate-200/70 space-y-1">
                    <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block">Ruangan</span>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">{{ $peminjaman->ruangan->nama_ruangan }}</h3>
                    <div class="text-slate-600 text-[11px]">
                        Kode: <strong class="text-slate-800 font-mono">{{ $peminjaman->ruangan->kode_ruangan }}</strong> • Lantai {{ $peminjaman->ruangan->lantai }} • Kapasitas: {{ $peminjaman->ruangan->kapasitas }} Orang
                    </div>
                </div>

                <!-- Inner Card: Peminjam -->
                <div class="p-3.5 bg-slate-50/80 rounded-lg border border-slate-200/70 space-y-1">
                    <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block">Identitas Peminjam</span>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">{{ $peminjaman->user->nama_lengkap }}</h3>
                    <div class="text-slate-600 text-[11px]">
                        <span class="uppercase font-semibold text-slate-700">{{ $peminjaman->user->jenis_pengguna }}</span> • 
                        <span class="font-mono text-slate-500">{{ $peminjaman->user->nomor_identitas }}</span>
                    </div>
                    <div class="text-[11px] text-slate-400">{{ $peminjaman->user->email }}</div>
                </div>
            </div>

            <!-- Inner Card 3: Alasan & Catatan Peminjaman -->
            <div class="p-3.5 bg-slate-50/80 rounded-lg border border-slate-200/70 space-y-1.5">
                <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block">Alasan / Tujuan Peminjaman</span>
                <div class="bg-white p-3 rounded-md border border-slate-200/60 text-xs text-slate-800 leading-relaxed whitespace-pre-line">
                    {{ $peminjaman->alasan_peminjaman }}
                </div>
                @if($peminjaman->catatan_tambahan)
                    <div class="pt-2 text-[11px] text-slate-500">
                        <strong class="text-slate-700">Catatan:</strong> {{ $peminjaman->catatan_tambahan }}
                    </div>
                @endif
            </div>

            <!-- Inner Card 4: Alasan Pembatalan (Jika Ada) -->
            @if($peminjaman->alasan_pembatalan)
                <div class="p-3.5 rounded-lg bg-rose-50/60 border border-rose-200/80 text-xs text-rose-800 space-y-1">
                    <div class="font-bold uppercase tracking-wider text-[10px]">Alasan Pembatalan:</div>
                    <p class="leading-relaxed bg-white/70 p-2.5 rounded border border-rose-100 text-[11px]">{{ $peminjaman->alasan_pembatalan }}</p>
                    @if($peminjaman->dibatalkan_pada)
                        <div class="text-[10px] text-rose-500 pt-0.5">Dibatalkan pada: {{ $peminjaman->dibatalkan_pada->format('d M Y, H:i') }} WIB</div>
                    @endif
                </div>
            @endif

            <!-- Inner Card 5: Opsi Batalkan Peminjaman Mandiri -->
            @if($peminjaman->status === 'disetujui' && !$peminjaman->isExpired() && auth()->id() === $peminjaman->user_id)
                <div class="p-3.5 rounded-lg border {{ $peminjaman->bisaDibatalkan() ? 'bg-amber-50/60 border-amber-200 text-amber-900' : 'bg-slate-50 border-slate-200 text-slate-600' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div>
                        <span class="font-bold block uppercase tracking-wider text-[10px] {{ $peminjaman->bisaDibatalkan() ? 'text-amber-800' : 'text-slate-500' }}">Ketentuan Pembatalan</span>
                        @if($peminjaman->bisaDibatalkan())
                            <p class="mt-0.5 text-[11px]">Dapat dibatalkan mandiri maks. 5 menit sebelum kegiatan (Batas akhir: <strong class="font-bold text-amber-900">{{ $peminjaman->deadline_pembatalan->format('H:i') }} WIB</strong>).</p>
                        @else
                            <p class="mt-0.5 text-[11px]">Batas waktu pembatalan mandiri (5 menit sebelum jam mulai pada {{ $peminjaman->deadline_pembatalan->format('H:i') }} WIB) telah berakhir.</p>
                        @endif
                    </div>
                    @if($peminjaman->bisaDibatalkan())
                        <form action="{{ route('peminjaman.cancel', $peminjaman) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan peminjaman ruangan ini?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded-lg text-xs transition cursor-pointer whitespace-nowrap shadow-xs">
                                Batalkan Peminjaman
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
