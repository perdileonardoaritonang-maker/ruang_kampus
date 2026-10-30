@extends('layouts.app')

@section('title', 'Riwayat Peminjaman Ruangan - EduSpace')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Riwayat Peminjaman Saya</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola status dan riwayat pengajuan peminjaman ruangan atau laboratorium Anda.</p>
        </div>
        <a href="{{ route('peminjaman.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-lg text-xs transition shadow-xs self-start sm:self-auto">
            + Pinjam Ruangan Baru
        </a>
    </div>

    <!-- Outer Card Container -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] overflow-hidden">
        <!-- Card Header -->
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/40">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Daftar Peminjaman Ruangan</h2>
            </div>
            <span class="text-[11px] font-mono text-slate-400 bg-white px-2 py-0.5 rounded border border-slate-200">Total: {{ $peminjamanList->total() }} Data</span>
        </div>

        <!-- Card Body: Nested Table Inside Inner Card Frame -->
        <div class="p-3 sm:p-4">
            <div class="border border-slate-200/80 rounded-lg overflow-hidden bg-white shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50/80 text-[11px] font-semibold text-slate-600 uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-3.5 py-2.5 border-r border-slate-200/70">Kode Booking</th>
                                <th class="px-3.5 py-2.5 border-r border-slate-200/70">Ruangan</th>
                                <th class="px-3.5 py-2.5 border-r border-slate-200/70">Jadwal Penggunaan</th>
                                <th class="px-3.5 py-2.5 border-r border-slate-200/70">Alasan / Tujuan</th>
                                <th class="px-3.5 py-2.5 border-r border-slate-200/70">Status</th>
                                <th class="px-3.5 py-2.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 bg-white">
                            @forelse($peminjamanList as $peminjaman)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <!-- Kode Booking -->
                                    <td class="px-3.5 py-2.5 font-mono font-bold text-slate-900 border-r border-slate-100 whitespace-nowrap">
                                        {{ $peminjaman->kode_booking }}
                                    </td>

                                    <!-- Ruangan -->
                                    <td class="px-3.5 py-2.5 border-r border-slate-100">
                                        <span class="font-bold text-slate-900 block leading-tight">{{ $peminjaman->ruangan->nama_ruangan }}</span>
                                        <span class="text-[10px] text-slate-400">Lt. {{ $peminjaman->ruangan->lantai }} • {{ $peminjaman->ruangan->kode_ruangan }}</span>
                                    </td>

                                    <!-- Jadwal -->
                                    <td class="px-3.5 py-2.5 border-r border-slate-100 whitespace-nowrap">
                                        <span class="font-semibold text-slate-800 block">{{ $peminjaman->tanggal->format('d M Y') }}</span>
                                        <span class="text-[10px] text-slate-500 font-mono">{{ substr($peminjaman->jam_mulai, 0, 5) }} - {{ substr($peminjaman->jam_selesai, 0, 5) }} WIB</span>
                                    </td>

                                    <!-- Alasan -->
                                    <td class="px-3.5 py-2.5 max-w-xs border-r border-slate-100">
                                        <p class="text-slate-600 line-clamp-1 text-[11px]">{{ $peminjaman->alasan_peminjaman }}</p>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-3.5 py-2.5 border-r border-slate-100 whitespace-nowrap">
                                        @if($peminjaman->status === 'disetujui' && !$peminjaman->isExpired())
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                ● Disetujui
                                            </span>
                                        @elseif($peminjaman->status === 'selesai' || $peminjaman->isExpired())
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-rose-50 text-rose-700 border border-rose-200" title="Waktu peminjaman telah lewat">
                                                Kedaluwarsa
                                            </span>
                                        @elseif($peminjaman->status === 'dibatalkan_user')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium uppercase bg-slate-100 text-slate-500 border border-slate-200">
                                                Dibatalkan Anda
                                            </span>
                                        @elseif($peminjaman->status === 'dibatalkan_biro')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium uppercase bg-rose-50 text-rose-700 border border-rose-200">
                                                Dibatalkan Biro
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium uppercase bg-slate-100 text-slate-600 border border-slate-200">
                                                Selesai
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Aksi -->
                                    <td class="px-3.5 py-2.5 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1">
                                            <a href="{{ route('peminjaman.show', $peminjaman) }}" class="px-2.5 py-1 text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded font-medium text-xs transition border border-slate-200">
                                                Detail
                                            </a>

                                            @if($peminjaman->status === 'disetujui' && !$peminjaman->isExpired())
                                                @if($peminjaman->bisaDibatalkan())
                                                    <form action="{{ route('peminjaman.cancel', $peminjaman) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan peminjaman ini? (Batas pembatalan: {{ $peminjaman->deadline_pembatalan->format('H:i') }} WIB)')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="px-2.5 py-1 text-rose-600 hover:bg-rose-50 border border-rose-200 rounded font-medium text-xs transition cursor-pointer" title="Dapat dibatalkan s.d. {{ $peminjaman->deadline_pembatalan->format('H:i') }} WIB">
                                                            Batal
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="px-2 py-1 text-[10px] text-slate-400 bg-slate-50 border border-slate-200 rounded cursor-not-allowed" title="Batas pembatalan lewat">
                                                        Terkunci
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400 text-xs">
                                        Belum ada riwayat peminjaman ruangan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($peminjamanList->hasPages())
                    <div class="px-4 py-2.5 border-t border-slate-100 bg-slate-50/50">
                        {{ $peminjamanList->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
