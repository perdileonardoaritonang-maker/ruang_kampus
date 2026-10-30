@extends('layouts.app')

@section('title', 'Jadwal Tetap Perkuliahan - Biro Pengelola')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Jadwal Tetap Perkuliahan & Agenda</h1>
            <p class="text-xs text-slate-500 mt-0.5">Jadwal perkuliahan mingguan dan pemeliharaan rutin yang mengunci slot ruangan.</p>
        </div>
        <a href="{{ route('biro.jadwal.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-lg text-xs transition shadow-sm">
            + Tambah Jadwal Tetap
        </a>
    </div>

    <!-- Filter Bar Card -->
    <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] flex flex-wrap gap-2.5 items-center">
        <form action="{{ route('biro.jadwal.index') }}" method="GET" class="flex flex-wrap gap-2.5 items-center flex-1">
            <select name="ruangan_id" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:outline-none">
                <option value="">Semua Ruangan</option>
                @foreach($allRuangan as $r)
                    <option value="{{ $r->id }}" {{ (string)$ruanganId === (string)$r->id ? 'selected' : '' }}>
                        {{ $r->kode_ruangan }} - {{ $r->nama_ruangan }}
                    </option>
                @endforeach
            </select>

            <select name="hari" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:outline-none">
                <option value="">Semua Hari</option>
                <option value="senin" {{ $hari === 'senin' ? 'selected' : '' }}>Senin</option>
                <option value="selasa" {{ $hari === 'selasa' ? 'selected' : '' }}>Selasa</option>
                <option value="rabu" {{ $hari === 'rabu' ? 'selected' : '' }}>Rabu</option>
                <option value="kamis" {{ $hari === 'kamis' ? 'selected' : '' }}>Kamis</option>
                <option value="jumat" {{ $hari === 'jumat' ? 'selected' : '' }}>Jumat</option>
                <option value="sabtu" {{ $hari === 'sabtu' ? 'selected' : '' }}>Sabtu</option>
            </select>

            <select name="jenis" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:outline-none">
                <option value="">Semua Jenis Kegiatan</option>
                <option value="kuliah" {{ $jenis === 'kuliah' ? 'selected' : '' }}>Kuliah</option>
                <option value="praktikum" {{ $jenis === 'praktikum' ? 'selected' : '' }}>Praktikum</option>
                <option value="acara_kampus" {{ $jenis === 'acara_kampus' ? 'selected' : '' }}>Acara Kampus</option>
                <option value="perawatan" {{ $jenis === 'perawatan' ? 'selected' : '' }}>Perawatan</option>
            </select>

            <button type="submit" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-lg text-xs transition cursor-pointer shadow-sm">
                Filter
            </button>
        </form>
    </div>

    <!-- Table Wrapped Inside Modern Card -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Daftar Jadwal Kampus</h2>
            <span class="text-xs text-slate-400">Total: {{ $jadwalList->total() }} Jadwal</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50 text-[11px] font-semibold text-slate-600 uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3.5 border-r border-slate-200">Nama Kegiatan</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Ruangan</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Pola & Hari</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Jam Pelaksanaan</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Masa Berlaku</th>
                        <th class="px-4 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700 bg-white">
                    @forelse($jadwalList as $item)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-4 py-3.5 border-r border-slate-200">
                                <span class="font-semibold text-slate-900 block">{{ $item->nama_kegiatan }}</span>
                                <span class="text-[10px] uppercase font-bold text-slate-500 mt-0.5 inline-block">{{ $item->jenis_jadwal }}</span>
                            </td>

                            <td class="px-4 py-3.5 border-r border-slate-200">
                                <span class="font-medium text-slate-800 block">{{ $item->ruangan->nama_ruangan }}</span>
                                <span class="text-[11px] text-slate-400">Lantai {{ $item->ruangan->lantai }} • {{ $item->ruangan->kode_ruangan }}</span>
                            </td>

                            <td class="px-4 py-3.5 border-r border-slate-200">
                                @if($item->tipe_pengulangan === 'mingguan')
                                    <span class="font-semibold text-slate-800 capitalize">Setiap {{ $item->hari }}</span>
                                @else
                                    <span class="font-semibold text-slate-800">{{ $item->tanggal_spesifik?->format('d M Y') }}</span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 font-medium text-slate-800 border-r border-slate-200">
                                {{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }} WIB
                            </td>

                            <td class="px-4 py-3.5 text-slate-400 text-[11px] border-r border-slate-200">
                                {{ $item->tanggal_mulai_efektif->format('d/m/y') }} s.d. {{ $item->tanggal_selesai_efektif->format('d/m/y') }}
                            </td>

                            <td class="px-4 py-3.5 text-center">
                                <form action="{{ route('biro.jadwal.destroy', $item) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 text-rose-600 hover:bg-rose-50 border border-rose-200 rounded font-medium text-xs transition cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                                Tidak ada jadwal perkuliahan atau agenda tetap.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jadwalList->hasPages())
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50">
                {{ $jadwalList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
