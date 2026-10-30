@extends('layouts.app')

@section('title', 'Manajemen Peminjaman Kampus - Biro Pengelola')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Manajemen Peminjaman Ruangan & Lab</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau peminjaman yang masih aktif dan riwayat peminjaman yang telah berakhir.</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 sm:gap-4" role="tablist" aria-label="Kategori peminjaman">
        <a
            href="{{ route('biro.peminjaman.index', array_merge(request()->except('tab', 'page'), ['tab' => 'aktif'])) }}"
            role="tab"
            aria-selected="{{ $tab === 'aktif' ? 'true' : 'false' }}"
            class="flex items-center justify-between gap-3 rounded-2xl border px-4 py-4 transition sm:px-5 {{ $tab === 'aktif' ? 'border-indigo-300 bg-indigo-50/80 shadow-sm ring-1 ring-indigo-100' : 'border-slate-200 bg-white hover:border-indigo-200 hover:bg-indigo-50/40' }}"
        >
            <span class="min-w-0">
                <span class="block text-sm font-bold {{ $tab === 'aktif' ? 'text-indigo-950' : 'text-slate-800' }}">Aktif / Sekarang</span>
                <span class="mt-1 block text-xs text-slate-500">Jadwal belum berakhir</span>
            </span>
            <span class="inline-flex h-9 min-w-9 shrink-0 items-center justify-center rounded-xl bg-white px-2 text-sm font-bold text-indigo-700 shadow-sm">{{ $activeCount }}</span>
        </a>
        <a
            href="{{ route('biro.peminjaman.index', array_merge(request()->except('tab', 'page'), ['tab' => 'kedaluwarsa'])) }}"
            role="tab"
            aria-selected="{{ $tab === 'kedaluwarsa' ? 'true' : 'false' }}"
            class="flex items-center justify-between gap-3 rounded-2xl border px-4 py-4 transition sm:px-5 {{ $tab === 'kedaluwarsa' ? 'border-amber-300 bg-amber-50/80 shadow-sm ring-1 ring-amber-100' : 'border-slate-200 bg-white hover:border-amber-200 hover:bg-amber-50/40' }}"
        >
            <span class="min-w-0">
                <span class="block text-sm font-bold {{ $tab === 'kedaluwarsa' ? 'text-amber-950' : 'text-slate-800' }}">Kedaluwarsa</span>
                <span class="mt-1 block text-xs text-slate-500">Jadwal telah selesai</span>
            </span>
            <span class="inline-flex h-9 min-w-9 shrink-0 items-center justify-center rounded-xl bg-white px-2 text-sm font-bold text-amber-700 shadow-sm">{{ $expiredCount }}</span>
        </a>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] p-4">
        <form action="{{ route('biro.peminjaman.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2.5">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <input 
                type="text" 
                name="q" 
                value="{{ $search }}" 
                placeholder="Cari kode booking, nama, NPM..." 
                class="px-3.5 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-1 focus:ring-slate-900 focus:outline-none"
            >

            <select name="ruangan_id" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:outline-none">
                <option value="">Semua Ruangan</option>
                @foreach($allRuangan as $r)
                    <option value="{{ $r->id }}" {{ (string)$ruanganId === (string)$r->id ? 'selected' : '' }}>
                        {{ $r->kode_ruangan }} - {{ $r->nama_ruangan }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:outline-none">
                <option value="">Semua Status</option>
                <option value="disetujui" {{ $status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="selesai" {{ $status === 'selesai' ? 'selected' : '' }}>Kedaluwarsa / Selesai</option>
                <option value="dibatalkan_user" {{ $status === 'dibatalkan_user' ? 'selected' : '' }}>Dibatalkan Mahasiswa</option>
                <option value="dibatalkan_biro" {{ $status === 'dibatalkan_biro' ? 'selected' : '' }}>Dibatalkan Biro</option>
            </select>

            <div class="flex gap-2">
                <input 
                    type="date" 
                    name="tanggal" 
                    value="{{ $tanggal }}" 
                    class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs focus:outline-none"
                >
                <button type="submit" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-lg text-xs transition cursor-pointer shadow-sm">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Table Wrapped Inside Modern Card -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">{{ $tab === 'aktif' ? 'Peminjaman Aktif / Sekarang' : 'Peminjaman Kedaluwarsa' }}</h2>
            <span class="text-xs text-slate-400">Total: {{ $peminjamanList->total() }} Data</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50 text-[11px] font-semibold text-slate-600 uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3.5 border-r border-slate-200">Kode & Tanggal</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Peminjam</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Ruangan</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Waktu</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Alasan Peminjaman</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Status</th>
                        <th class="px-4 py-3.5 text-center">Aksi Biro</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700 bg-white">
                    @forelse($peminjamanList as $item)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-4 py-3.5 border-r border-slate-200">
                                <span class="font-mono font-semibold text-slate-900 block">{{ $item->kode_booking }}</span>
                                <span class="text-[11px] text-slate-400">{{ $item->tanggal->format('d/m/Y') }}</span>
                            </td>

                            <td class="px-4 py-3.5 border-r border-slate-200">
                                <span class="font-semibold text-slate-900 block">{{ $item->user->nama_lengkap }}</span>
                                <span class="text-[11px] text-slate-500 font-mono">{{ $item->user->nomor_identitas }} ({{ ucfirst($item->user->jenis_pengguna) }})</span>
                            </td>

                            <td class="px-4 py-3.5 border-r border-slate-200">
                                <span class="font-medium text-slate-800 block">{{ $item->ruangan->nama_ruangan }}</span>
                                <span class="text-[11px] text-slate-400">Lantai {{ $item->ruangan->lantai }} • {{ $item->ruangan->kode_ruangan }}</span>
                            </td>

                            <td class="px-4 py-3.5 font-medium text-slate-800 border-r border-slate-200">
                                {{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }} WIB
                            </td>

                            <td class="px-4 py-3.5 max-w-xs border-r border-slate-200">
                                <p class="text-slate-600 line-clamp-1">{{ $item->alasan_peminjaman }}</p>
                            </td>

                            <td class="px-4 py-3.5 border-r border-slate-200">
                                @if($item->status === 'disetujui' && !$item->isExpired())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        ● Disetujui
                                    </span>
                                @elseif($item->status === 'selesai' || $item->isExpired())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-xs" title="Waktu kegiatan telah lewat">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Kedaluwarsa
                                    </span>
                                @elseif($item->status === 'dibatalkan_user')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                        Batal (User)
                                    </span>
                                @elseif($item->status === 'dibatalkan_biro')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                        Batal (Biro)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                        Selesai
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('peminjaman.show', $item) }}" class="px-2.5 py-1 text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded font-medium transition border border-slate-200">
                                        Detail
                                    </a>

                                    @if($item->status === 'disetujui' && !$item->isExpired())
                                        <button 
                                            type="button" 
                                            onclick="openBiroCancelModal('{{ $item->id }}', '{{ $item->kode_booking }}', '{{ $item->user->nama_lengkap }}')"
                                            class="px-2.5 py-1 text-rose-600 hover:bg-rose-50 border border-rose-200 rounded font-medium transition cursor-pointer"
                                        >
                                            Batalkan
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                                {{ $tab === 'aktif' ? 'Tidak ada peminjaman aktif yang sesuai kriteria.' : 'Tidak ada peminjaman kedaluwarsa yang sesuai kriteria.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($peminjamanList->hasPages())
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50">
                {{ $peminjamanList->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Pembatalan Resmi Biro (Clean Minimalist) -->
<div id="cancelBiroModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-5 shadow-xl border border-slate-100">
        <h3 class="text-sm font-bold text-slate-900 mb-1">Batalkan Peminjaman (Biro)</h3>
        <p class="text-xs text-slate-500 mb-4">
            Kode: <span id="modalBookingCode" class="font-mono font-semibold text-slate-800"></span> • Peminjam: <span id="modalUserName" class="font-medium text-slate-800"></span>
        </p>

        <form id="cancelBiroForm" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="alasan_pembatalan" class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">
                    Alasan Pembatalan Resmi (Wajib)
                </label>
                <textarea 
                    name="alasan_pembatalan" 
                    id="alasan_pembatalan" 
                    rows="3" 
                    required 
                    placeholder="Contoh: Digunakan mendadak untuk agenda Ujian Sidang Skripsi Kampus..."
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs focus:ring-1 focus:ring-rose-500 focus:outline-none"
                ></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-1">
                <button 
                    type="button" 
                    onclick="closeBiroCancelModal()"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition cursor-pointer"
                >
                    Tutup
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded-lg text-xs transition cursor-pointer shadow-sm"
                >
                    Batalkan Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openBiroCancelModal(id, code, userName) {
        document.getElementById('modalBookingCode').textContent = code;
        document.getElementById('modalUserName').textContent = userName;
        document.getElementById('cancelBiroForm').action = `/biro/peminjaman/${id}/batal`;
        document.getElementById('cancelBiroModal').classList.remove('hidden');
    }

    function closeBiroCancelModal() {
        document.getElementById('cancelBiroModal').classList.add('hidden');
        document.getElementById('alasan_pembatalan').value = '';
    }
</script>
@endsection
