@extends('layouts.app')

@section('title', $ruangan->nama_ruangan . ' - Detail Ruangan')

@section('content')
<div class="space-y-8">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('ruangan.index') }}" class="hover:text-indigo-600 transition">&larr; Kembali ke Katalog Ruangan</a>
        <span>/</span>
        <span class="text-slate-800 font-bold">Lantai {{ $ruangan->lantai }}</span>
        <span>/</span>
        <span class="text-slate-800 font-bold">{{ $ruangan->kode_ruangan }}</span>
    </div>

    <!-- Main Room Detail Header -->
    <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-200/80 shadow-sm grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3 py-1 rounded-lg text-xs font-bold bg-slate-900 text-white">
                    Lantai {{ $ruangan->lantai }}
                </span>
                <span class="px-3 py-1 rounded-lg text-xs font-bold uppercase {{ $ruangan->jenis_ruangan === 'laboratorium' ? 'bg-indigo-600 text-white' : 'bg-emerald-600 text-white' }}">
                    {{ $ruangan->jenis_ruangan === 'laboratorium' ? 'Laboratorium' : 'Ruang Kuliah' }}
                </span>
                @if($ruangan->status_operasional === 'aktif')
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800">
                        ● Operasional & Aktif
                    </span>
                @else
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-800">
                        ● {{ ucfirst($ruangan->status_operasional) }}
                    </span>
                @endif
            </div>

            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $ruangan->nama_ruangan }}</h1>
            <p class="text-sm text-slate-600 leading-relaxed">{{ $ruangan->lokasi_keterangan }}</p>

            @if($ruangan->keterangan_tambahan)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-600 leading-relaxed">
                    <strong class="text-slate-800 block mb-1">Keterangan Fasilitas:</strong>
                    {{ $ruangan->keterangan_tambahan }}
                </div>
            @endif

            <!-- Spesifikasi Dasar -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-2">
                <div class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100/60">
                    <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider block">Kapasitas Maksimal</span>
                    <span class="text-2xl font-extrabold text-slate-900">{{ $ruangan->kapasitas }} <span class="text-xs font-semibold text-slate-500">Orang</span></span>
                </div>
                @if($ruangan->jenis_ruangan === 'laboratorium')
                    <div class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100/60">
                        <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider block">Jumlah Komputer PC</span>
                        <span class="text-2xl font-extrabold text-indigo-700">{{ $ruangan->jumlah_komputer }} <span class="text-xs font-semibold text-slate-500">Unit</span></span>
                    </div>
                @endif
                <div class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100/60">
                    <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider block">Lokasi Lantai</span>
                    <span class="text-2xl font-extrabold text-slate-900">Lantai {{ $ruangan->lantai }}</span>
                </div>
            </div>
        </div>

        <!-- Sidebar Aksi Pinjam & Laporkan -->
        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200/80 flex flex-col justify-between space-y-6">
            <div class="space-y-3">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Aksi Cepat</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Periksa jadwal di bawah sebelum mengajukan peminjaman ruangan. Peminjaman otomatis disetujui jika seluruh syarat terpenuhi.
                </p>
            </div>

            <div class="space-y-3">
                @auth
                    @if(!auth()->user()->isBiro() && $ruangan->status_operasional === 'aktif')
                        <a href="{{ route('peminjaman.create', ['ruangan_id' => $ruangan->id]) }}" class="block w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-center text-xs shadow-md shadow-indigo-200 transition">
                            Ajukan Peminjaman Ruangan
                        </a>
                    @endif
                    <a href="{{ route('laporan.create', ['ruangan_id' => $ruangan->id]) }}" class="block w-full py-2.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 font-semibold rounded-xl text-center text-xs transition">
                        Laporkan Kendala / Kerusakan
                    </a>
                @else
                    <a href="{{ route('login') }}" class="block w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-center text-xs shadow-md shadow-indigo-200 transition">
                        Masuk untuk Meminjam
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Fasilitas Ruangan & Jadwal Terisi Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Kolom Fasilitas -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900">Daftar Fasilitas Tersedia</h2>
            <div class="divide-y divide-slate-100">
                @forelse($ruangan->fasilitas as $fasilitas)
                    <div class="py-3 flex items-center justify-between text-xs">
                        <div class="font-semibold text-slate-800">{{ $fasilitas->nama_fasilitas }}</div>
                        <div class="text-right">
                            <span class="font-bold text-indigo-600">{{ $fasilitas->pivot->jumlah }} Unit</span>
                            <span class="block text-[10px] text-emerald-600 uppercase font-bold">{{ $fasilitas->pivot->kondisi }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Belum ada rincian fasilitas tercatat.</p>
                @endforelse
            </div>
        </div>

        <!-- Kolom Jadwal Kuliah & Peminjaman Mendatang -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-6">
            <!-- Jadwal Tetap Kampus -->
            <div>
                <h2 class="text-base font-bold text-slate-900 mb-2">Jadwal Kuliah / Praktikum Rutin</h2>
                <div class="divide-y divide-slate-100">
                    @forelse($ruangan->jadwalTetap as $jadwal)
                        <div class="py-3 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-900 text-sm">{{ $jadwal->nama_kegiatan }}</span>
                                <div class="text-slate-500 mt-0.5">
                                    <span class="capitalize font-semibold text-indigo-600">{{ $jadwal->hari ?? $jadwal->tanggal_spesifik?->format('d M Y') }}</span>
                                    <span>•</span>
                                    <span>{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WIB</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                {{ $jadwal->jenis_jadwal }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-3 text-center">Tidak ada jadwal perkuliahan tetap yang mengikat ruangan ini.</p>
                    @endforelse
                </div>
            </div>

            <!-- Peminjaman Mendatang yang Sudah Disetujui -->
            <div class="pt-4 border-t border-slate-100">
                <h2 class="text-base font-bold text-slate-900 mb-2">Peminjaman Disetujui Mendatang</h2>
                <div class="divide-y divide-slate-100">
                    @forelse($upcomingBookings as $bkg)
                        <div class="py-3 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-800">{{ $bkg->alasan_peminjaman }}</span>
                                <div class="text-slate-500 mt-0.5">
                                    <span>{{ $bkg->tanggal->format('d M Y') }}</span>
                                    <span>•</span>
                                    <span class="font-semibold text-slate-700">{{ substr($bkg->jam_mulai, 0, 5) }} - {{ substr($bkg->jam_selesai, 0, 5) }} WIB</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                Disetujui
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-3 text-center">Belum ada peminjaman lain yang terjadwal.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
