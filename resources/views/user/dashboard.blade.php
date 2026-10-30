@extends('layouts.app')

@section('title', 'Dashboard Pengguna - EduSpace')

@section('content')
<div class="space-y-4">
    <!-- Profil & Status Verifikasi Card -->
    <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-800 font-bold text-sm">
                {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">{{ $user->nama_lengkap }}</h1>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $user->jenis_pengguna === 'dosen' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                        {{ $user->jenis_pengguna === 'dosen' ? 'Dosen' : 'Mahasiswa' }}
                    </span>
                </div>
                <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                    {{ $user->jenis_pengguna === 'mahasiswa' ? 'NPM' : 'NIDN/NIP' }}: <strong class="text-slate-700">{{ $user->nomor_identitas }}</strong>
                    <span class="mx-1">•</span>
                    <span>{{ $user->email }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($user->canBookRoom())
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Akun Terverifikasi
                </span>
            @else
                <a href="{{ route('verification.identity.notice') }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/80 hover:bg-amber-100 transition">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    Menunggu Verifikasi Biro
                </a>
            @endif
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition cursor-pointer" title="Keluar">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Keluar
                </button>
            </form>
        </div>
    </div>

    <details id="panduan-peminjaman" class="bg-white rounded-xl border border-blue-100 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] overflow-hidden scroll-mt-24">
        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 bg-blue-50/50 px-4 py-3 hover:bg-blue-50 transition">
            <span class="flex items-center gap-2.5">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-700" aria-hidden="true">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16ZM4 17.5A2.5 2.5 0 0 1 6.5 15H20M8 7h8m-8 4h6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span><strong class="block text-xs font-bold text-slate-800">Panduan Peminjaman Ruangan</strong><small class="mt-0.5 block text-[10px] text-slate-500">Lihat langkah-langkah mengajukan peminjaman.</small></span>
            </span>
            <span class="shrink-0 text-[11px] font-semibold text-blue-700">Buka panduan <span aria-hidden="true">⌄</span></span>
        </summary>
        <div class="grid grid-cols-1 gap-2 border-t border-slate-100 p-3 sm:grid-cols-3">
            <article class="rounded-lg border border-slate-100 bg-slate-50/70 p-3">
                <span class="text-[10px] font-bold tracking-wide text-blue-600">LANGKAH 1</span>
                <h2 class="mt-1 text-xs font-bold text-slate-800">Pilih ruangan dan jadwal</h2>
                <p class="mt-1 text-[11px] leading-relaxed text-slate-500">Buka katalog, tentukan tanggal dan waktu, lalu cek status ketersediaan ruangan.</p>
                <a href="{{ route('ruangan.index') }}" class="mt-2 inline-flex text-[10px] font-semibold text-blue-700 hover:text-blue-800">Buka katalog <span class="ml-1" aria-hidden="true">→</span></a>
            </article>
            <article class="rounded-lg border border-slate-100 bg-slate-50/70 p-3">
                <span class="text-[10px] font-bold tracking-wide text-blue-600">LANGKAH 2</span>
                <h2 class="mt-1 text-xs font-bold text-slate-800">Lengkapi pengajuan</h2>
                <p class="mt-1 text-[11px] leading-relaxed text-slate-500">Pilih ruangan, isi jadwal, tujuan peminjaman, dan informasi kegiatan dengan benar.</p>
                @if($user->canBookRoom())
                    <a href="{{ route('peminjaman.create') }}" class="mt-2 inline-flex text-[10px] font-semibold text-blue-700 hover:text-blue-800">Ajukan peminjaman <span class="ml-1" aria-hidden="true">→</span></a>
                @else
                    <a href="{{ route('verification.identity.notice') }}" class="mt-2 inline-flex text-[10px] font-semibold text-amber-700 hover:text-amber-800">Cek verifikasi akun <span class="ml-1" aria-hidden="true">→</span></a>
                @endif
            </article>
            <article class="rounded-lg border border-slate-100 bg-slate-50/70 p-3">
                <span class="text-[10px] font-bold tracking-wide text-blue-600">LANGKAH 3</span>
                <h2 class="mt-1 text-xs font-bold text-slate-800">Pantau status pengajuan</h2>
                <p class="mt-1 text-[11px] leading-relaxed text-slate-500">Periksa status dan detail peminjaman melalui menu Peminjaman Saya.</p>
                <a href="{{ route('peminjaman.index') }}" class="mt-2 inline-flex text-[10px] font-semibold text-blue-700 hover:text-blue-800">Lihat peminjaman <span class="ml-1" aria-hidden="true">→</span></a>
            </article>
        </div>
    </details>

    <!-- Outer Card: Status Peminjaman Aktif -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/40">
            <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Status Peminjaman Aktif</h2>
            <span class="text-[10px] text-slate-400">Maks. 1 Peminjaman Aktif</span>
        </div>

        <div class="p-4">
            @if($activeBooking)
                <!-- Inner Card: Peminjaman Aktif -->
                <div class="bg-slate-50/80 rounded-lg border border-slate-200/70 p-3.5 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold text-slate-900 bg-white px-2 py-0.5 rounded border border-slate-200">
                                {{ $activeBooking->kode_booking }}
                            </span>
                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700 bg-emerald-100/70 px-2 py-0.5 rounded">
                                ● Disetujui Otomatis
                            </span>
                        </div>

                        <h3 class="text-sm sm:text-base font-bold text-slate-900 pt-0.5">
                            {{ $activeBooking->ruangan->nama_ruangan }}
                        </h3>

                        <p class="text-xs text-slate-500">
                            Lantai {{ $activeBooking->ruangan->lantai }} • Jadwal: <strong class="text-slate-800">{{ $activeBooking->tanggal->format('d M Y') }}</strong> ({{ substr($activeBooking->jam_mulai, 0, 5) }} - {{ substr($activeBooking->jam_selesai, 0, 5) }} WIB)
                        </p>

                        <p class="text-[11px] text-slate-600 italic">
                            "{{ $activeBooking->alasan_peminjaman }}"
                        </p>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('peminjaman.show', $activeBooking) }}" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-lg text-xs transition shadow-xs">
                            Detail Peminjaman &rarr;
                        </a>
                    </div>
                </div>

                <p class="text-[10px] text-slate-400 mt-2.5">
                    ℹ️ Anda sedang memiliki 1 peminjaman aktif. Peminjaman ruangan lain dapat diajukan setelah waktu peminjaman di atas selesai atau dibatalkan.
                </p>
            @else
                <div class="py-5 text-center bg-slate-50/50 rounded-lg border border-dashed border-slate-200">
                    <p class="text-xs text-slate-500">Tidak ada peminjaman ruangan yang sedang berlangsung.</p>
                    @if($user->canBookRoom())
                        <div class="mt-2.5">
                            <a href="{{ route('peminjaman.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-lg text-xs transition shadow-xs">
                                + Ajukan Peminjaman Ruangan
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Ringkasan Riwayat Peminjaman & Laporan di dalam Card Modern (Cards Inside Cards) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Outer Card: Riwayat Peminjaman -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/40">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Riwayat Peminjaman Terakhir</h3>
                <a href="{{ route('peminjaman.index') }}" class="text-[11px] font-semibold text-slate-500 hover:text-slate-900">Lihat Semua &rarr;</a>
            </div>
            <div class="p-3.5 space-y-2 text-xs">
                @forelse($recentBookings as $bkg)
                    <!-- Inner Card Peminjaman -->
                    <div class="bg-slate-50/70 border border-slate-200/60 rounded-lg p-2.5 hover:bg-slate-100/70 transition flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <a href="{{ route('peminjaman.show', $bkg) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition block truncate">{{ $bkg->ruangan->nama_ruangan }}</a>
                            <span class="text-[10px] text-slate-400">{{ $bkg->tanggal->format('d/m/Y') }} • {{ substr($bkg->jam_mulai, 0, 5) }}-{{ substr($bkg->jam_selesai, 0, 5) }} WIB</span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase shrink-0 {{ $bkg->status === 'disetujui' && !$bkg->isExpired() ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($bkg->status === 'selesai' || $bkg->isExpired() ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600 border border-slate-200') }}">
                            {{ $bkg->status === 'selesai' || $bkg->isExpired() ? 'Kedaluwarsa' : str_replace('_', ' ', $bkg->status) }}
                        </span>
                    </div>
                @empty
                    <div class="py-5 text-center text-slate-400 text-xs">Belum ada riwayat peminjaman.</div>
                @endforelse
            </div>
        </div>

        <!-- Outer Card: Laporan Kerusakan -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/40">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Laporan Kerusakan Saya</h3>
                <a href="{{ route('laporan.index') }}" class="text-[11px] font-semibold text-slate-500 hover:text-slate-900">Lihat Semua &rarr;</a>
            </div>
            <div class="p-3.5 space-y-2 text-xs">
                @forelse($recentReports as $rpt)
                    <!-- Inner Card Laporan -->
                    <div class="bg-slate-50/70 border border-slate-200/60 rounded-lg p-2.5 hover:bg-slate-100/70 transition flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <a href="{{ route('laporan.show', $rpt) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition block truncate">{{ $rpt->judul_laporan }}</a>
                            <span class="text-[10px] text-slate-400">{{ $rpt->ruangan->nama_ruangan }} • {{ $rpt->created_at->format('d/m/Y') }}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase shrink-0 {{ $rpt->status === 'selesai' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ str_replace('_', ' ', $rpt->status) }}
                        </span>
                    </div>
                @empty
                    <div class="py-5 text-center text-slate-400 text-xs">Belum ada laporan kerusakan diajukan.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
