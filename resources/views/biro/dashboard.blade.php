@extends('layouts.app')

@section('title', 'Dashboard Biro Pengelola - EduSpace')

@section('content')
<div class="space-y-6">
    <!-- Header Ringkas -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">Panel Administratif Biro</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-900 text-white">Administrator Kampus</span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Ringkasan operasional sarana gedung kampus, verifikasi pengguna, dan agenda ruangan.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <span class="text-xs font-medium text-slate-500">{{ date('d M Y') }}</span>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition shadow-sm cursor-pointer" title="Keluar">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Keluar Akun
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Stats Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        <!-- Verifikasi Akun -->
        <a href="{{ route('biro.verifikasi.index', ['status' => 'menunggu']) }}" class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] hover:border-slate-300 transition block">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Verifikasi Akun</span>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['users_menunggu_verifikasi'] }}</div>
            <span class="text-[11px] text-amber-600 font-medium mt-0.5 block">Perlu ditinjau &rarr;</span>
        </a>

        <!-- Total Ruangan -->
        <a href="{{ route('biro.ruangan.index') }}" class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] hover:border-slate-300 transition block">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Total Ruangan</span>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total_ruangan'] }} <span class="text-xs font-normal text-slate-400">({{ $stats['total_lab'] }} Lab)</span></div>
            <span class="text-[11px] text-slate-500 font-medium mt-0.5 block">{{ $stats['total_lantai'] }} lantai terdaftar &rarr;</span>
        </a>

        <!-- Peminjaman Hari Ini -->
        <a href="{{ route('biro.peminjaman.index') }}" class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] hover:border-slate-300 transition block">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Peminjaman Hari Ini</span>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['peminjaman_hari_ini'] }}</div>
            <span class="text-[11px] text-emerald-600 font-medium mt-0.5 block">Agenda aktif &rarr;</span>
        </a>

        <!-- Laporan Kerusakan -->
        <a href="{{ route('biro.laporan.index') }}" class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] hover:border-slate-300 transition block">
            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Laporan Masuk</span>
            <div class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['laporan_pending'] }}</div>
            <span class="text-[11px] text-rose-600 font-medium mt-0.5 block">Belum selesai &rarr;</span>
        </a>
    </div>

    <!-- 2 Column Section: Peminjaman Hari Ini & Laporan Pending Inside Modern Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Peminjaman Hari Ini -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Agenda Peminjaman Hari Ini</h2>
                <a href="{{ route('biro.peminjaman.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-900">Lihat Semua &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100 text-xs">
                @forelse($todayBookings as $bkg)
                    <div class="px-5 py-3 flex items-center justify-between hover:bg-slate-50/50 transition">
                        <div>
                            <span class="font-semibold text-slate-900 block">{{ $bkg->ruangan->nama_ruangan }}</span>
                            <span class="text-[11px] text-slate-500">{{ $bkg->user->nama_lengkap }} • {{ substr($bkg->jam_mulai, 0, 5) }} - {{ substr($bkg->jam_selesai, 0, 5) }} WIB</span>
                        </div>
                        <span class="font-mono text-[10px] text-slate-400 font-semibold">{{ $bkg->kode_booking }}</span>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-slate-400">Tidak ada peminjaman ruangan yang terjadwal untuk hari ini.</div>
                @endforelse
            </div>
        </div>

        <!-- Laporan Kerusakan Masuk -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Laporan Kerusakan Terbaru</h2>
                <a href="{{ route('biro.laporan.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-900">Lihat Semua &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100 text-xs">
                @forelse($recentReports as $rpt)
                    <div class="px-5 py-3 flex items-center justify-between hover:bg-slate-50/50 transition">
                        <div>
                            <span class="font-semibold text-slate-900 block">{{ $rpt->judul_laporan }}</span>
                            <span class="text-[11px] text-slate-400">{{ $rpt->ruangan->nama_ruangan }} (Lantai {{ $rpt->ruangan->lantai }}) • {{ $rpt->user->nama_lengkap }}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $rpt->tingkat_urgensi === 'darurat' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $rpt->tingkat_urgensi }}
                        </span>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-slate-400">Seluruh fasilitas ruangan dalam kondisi baik.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
