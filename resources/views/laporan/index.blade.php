@extends('layouts.app')

@section('title', 'Laporan Kerusakan Fasilitas - EduSpace')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Laporan Kerusakan Fasilitas</h1>
            <p class="text-xs text-slate-500 mt-0.5">Sampaikan kendala fasilitas AC, proyektor, atau perangkat lab pada ruangan yang Anda gunakan.</p>
        </div>
        <a href="{{ route('laporan.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-lg text-xs transition shadow-xs self-start sm:self-auto">
            + Buat Laporan Baru
        </a>
    </div>

    <!-- Outer Card Container -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] overflow-hidden">
        <!-- Card Header Strip -->
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/40">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Riwayat Laporan Kerusakan Saya</h2>
            </div>
            <span class="text-[11px] font-mono text-slate-400 bg-white px-2 py-0.5 rounded border border-slate-200">Total: {{ $laporanList->total() }} Laporan</span>
        </div>

        <!-- Card Body: Nested Table Inside Inner Card Frame -->
        <div class="p-3 sm:p-4">
            <div class="border border-slate-200/80 rounded-lg overflow-hidden bg-white shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50/80 text-[11px] font-semibold text-slate-600 uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-3.5 py-2.5 border-r border-slate-200/70">Kode & Tanggal</th>
                                <th class="px-3.5 py-2.5 border-r border-slate-200/70">Ruangan</th>
                                <th class="px-3.5 py-2.5 border-r border-slate-200/70">Masalah / Fasilitas</th>
                                <th class="px-3.5 py-2.5 border-r border-slate-200/70">Urgensi</th>
                                <th class="px-3.5 py-2.5 border-r border-slate-200/70">Status Penanganan</th>
                                <th class="px-3.5 py-2.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 bg-white">
                            @forelse($laporanList as $item)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-3.5 py-2.5 border-r border-slate-100 whitespace-nowrap">
                                        <a href="{{ route('laporan.show', $item) }}" class="font-mono font-bold text-slate-900 hover:text-indigo-600 block">{{ $item->kode_laporan }}</a>
                                        <span class="text-[10px] text-slate-400">{{ $item->created_at->format('d/m/Y') }}</span>
                                    </td>

                                    <td class="px-3.5 py-2.5 border-r border-slate-100">
                                        <span class="font-bold text-slate-900 block leading-tight">{{ $item->ruangan->nama_ruangan }}</span>
                                        <span class="text-[10px] text-slate-400">Lt. {{ $item->ruangan->lantai }} • {{ $item->ruangan->kode_ruangan }}</span>
                                    </td>

                                    <td class="px-3.5 py-2.5 max-w-xs border-r border-slate-100">
                                        <span class="font-bold text-slate-900 block truncate">{{ $item->judul_laporan }}</span>
                                        <span class="text-slate-500 line-clamp-1 text-[11px]">{{ $item->deskripsi_masalah }}</span>
                                    </td>

                                    <td class="px-3.5 py-2.5 border-r border-slate-100 whitespace-nowrap">
                                        @if($item->tingkat_urgensi === 'darurat')
                                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-rose-50 text-rose-700 border border-rose-200">
                                                ● Darurat
                                            </span>
                                        @elseif($item->tingkat_urgensi === 'tinggi')
                                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">
                                                ● Tinggi
                                            </span>
                                        @elseif($item->tingkat_urgensi === 'sedang')
                                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-blue-50 text-blue-700 border border-blue-200">
                                                Sedang
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-slate-100 text-slate-600 border border-slate-200">
                                                Rendah
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-3.5 py-2.5 border-r border-slate-100 whitespace-nowrap">
                                        @if($item->status === 'menunggu')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">
                                                Menunggu Tinjauan
                                            </span>
                                        @elseif($item->status === 'sedang_ditangani')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200">
                                                Sedang Ditangani
                                            </span>
                                        @elseif($item->status === 'selesai')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Selesai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-rose-50 text-rose-700 border border-rose-200">
                                                Ditolak
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-3.5 py-2.5 text-center whitespace-nowrap">
                                        <a href="{{ route('laporan.show', $item) }}" class="px-2.5 py-1 text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded font-medium text-xs transition border border-slate-200">
                                            Detail &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400 text-xs">
                                        Anda belum memiliki laporan kerusakan fasilitas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($laporanList->hasPages())
                    <div class="px-4 py-2.5 border-t border-slate-100 bg-slate-50/50">
                        {{ $laporanList->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
