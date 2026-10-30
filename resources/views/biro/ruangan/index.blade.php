@extends('layouts.app')

@section('title', 'Manajemen Ruangan Kampus - Biro Pengelola')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Manajemen Ruangan Kampus</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data ruangan kuliah dan laboratorium komputer kampus berdasarkan lantai.</p>
        </div>
        <a href="{{ route('biro.ruangan.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-lg text-xs transition shadow-sm">
            + Tambah Ruangan Baru
        </a>
    </div>

    <!-- Ringkasan hanya menampilkan lantai yang memiliki data ruangan -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-2.5">
        @foreach($floorSummary as $floor => $item)
            @php
                $total = $item->total;
                $kuliah = $item->total_kuliah;
                $lab = $item->total_lab;
                $isSelected = (string)$lantai === (string)$floor;
            @endphp
            <a href="{{ route('biro.ruangan.index', array_merge(request()->except('lantai', 'page'), ['lantai' => $floor])) }}" 
               class="p-3 rounded-xl border transition {{ $isSelected ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-white hover:border-slate-300 text-slate-800 border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)]' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider {{ $isSelected ? 'text-slate-300' : 'text-slate-500' }}">Lantai {{ $floor }}</span>
                    <span class="text-xs font-bold {{ $isSelected ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700' }} px-1.5 py-0.5 rounded">{{ $total }}</span>
                </div>
                <div class="mt-2 text-[10px] space-y-0.5 {{ $isSelected ? 'text-slate-300' : 'text-slate-500' }}">
                    <div class="flex justify-between">
                        <span>Kuliah:</span>
                        <strong class="{{ $isSelected ? 'text-white' : 'text-slate-800' }}">{{ $kuliah }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Lab:</span>
                        <strong class="{{ $isSelected ? 'text-indigo-300' : 'text-indigo-600' }}">{{ $lab }}</strong>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Filter Bar Card -->
    <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] flex flex-wrap gap-2.5 items-center justify-between">
        <form action="{{ route('biro.ruangan.index') }}" method="GET" class="flex flex-wrap gap-2 items-center flex-1">
            <input 
                type="text" 
                name="q" 
                value="{{ $search }}" 
                placeholder="Cari kode atau nama ruangan..." 
                class="px-3.5 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-1 focus:ring-slate-900 focus:outline-none min-w-[200px]"
            >

            <select name="lantai" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:outline-none">
                <option value="">Semua Lantai</option>
                @foreach($floorSummary as $floor => $item)
                    <option value="{{ $floor }}" {{ (string)$lantai === (string)$floor ? 'selected' : '' }}>Lantai {{ $floor }} ({{ $item->total }})</option>
                @endforeach
            </select>

            <select name="jenis_ruangan" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:outline-none">
                <option value="">Semua Jenis</option>
                <option value="ruang_kuliah" {{ $jenisRuangan === 'ruang_kuliah' ? 'selected' : '' }}>Ruang Kuliah</option>
                <option value="laboratorium" {{ $jenisRuangan === 'laboratorium' ? 'selected' : '' }}>Laboratorium</option>
            </select>

            <select name="status" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:outline-none">
                <option value="">Semua Status</option>
                <option value="aktif" {{ $status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="perawatan" {{ $status === 'perawatan' ? 'selected' : '' }}>Perawatan</option>
                <option value="nonaktif" {{ $status === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            <button type="submit" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-lg text-xs transition cursor-pointer shadow-sm">
                Filter
            </button>

            @if($search || $lantai || $status || $jenisRuangan)
                <a href="{{ route('biro.ruangan.index') }}" class="px-2.5 py-1.5 text-xs text-slate-500 hover:text-slate-800 transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table Wrapped Inside Modern Card -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Daftar Ruangan Kampus</h2>
            <span class="text-xs text-slate-400">Total: {{ $ruanganList->total() }} Ruangan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50 text-[11px] font-semibold text-slate-600 uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3.5 border-r border-slate-200">Kode & Nama Ruangan</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Lantai & Tipe</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Kapasitas</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Fasilitas</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Status</th>
                        <th class="px-4 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700 bg-white">
                    @forelse($ruanganList as $ruangan)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-4 py-3.5 border-r border-slate-200">
                                <span class="font-mono font-semibold text-slate-900 block">{{ $ruangan->kode_ruangan }}</span>
                                <span class="font-medium text-slate-800 block">{{ $ruangan->nama_ruangan }}</span>
                                <span class="text-[11px] text-slate-400">{{ $ruangan->lokasi_keterangan }}</span>
                            </td>

                            <td class="px-4 py-3.5 border-r border-slate-200">
                                <span class="font-semibold text-slate-800 block">Lantai {{ $ruangan->lantai }}</span>
                                <span class="text-[11px] text-slate-500 capitalize">{{ str_replace('_', ' ', $ruangan->jenis_ruangan) }}</span>
                            </td>

                            <td class="px-4 py-3.5 border-r border-slate-200">
                                <span class="font-semibold text-slate-900 block">{{ $ruangan->kapasitas }} Orang</span>
                                @if($ruangan->jenis_ruangan === 'laboratorium')
                                    <span class="text-[11px] text-indigo-600 font-medium">{{ $ruangan->jumlah_komputer }} PC</span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 border-r border-slate-200">
                                <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $ruangan->fasilitas->count() }} Fasilitas
                                </span>
                            </td>

                            <td class="px-4 py-3.5 border-r border-slate-200">
                                <form action="{{ route('biro.ruangan.toggle-status', $ruangan) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="cursor-pointer" title="Klik untuk mengubah status">
                                        @if($ruangan->status_operasional === 'aktif')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                ● Aktif
                                            </span>
                                        @elseif($ruangan->status_operasional === 'perawatan')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                ● Perawatan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                                ● Nonaktif
                                            </span>
                                        @endif
                                    </button>
                                </form>
                            </td>

                            <td class="px-4 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('ruangan.show', $ruangan) }}" class="px-2.5 py-1 text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded font-medium transition border border-slate-200">
                                        Lihat
                                    </a>
                                    <a href="{{ route('biro.ruangan.edit', $ruangan) }}" class="px-2.5 py-1 text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded font-medium transition border border-slate-200">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                                Belum ada ruangan tercatat dalam sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ruanganList->hasPages())
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50">
                {{ $ruanganList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
