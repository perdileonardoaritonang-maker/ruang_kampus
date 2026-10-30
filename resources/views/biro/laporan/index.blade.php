@extends('layouts.app')

@section('title', 'Penanganan Laporan Kerusakan - Biro Pengelola')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Penanganan Laporan Kerusakan</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola laporan kendala dan kerusakan fasilitas dari mahasiswa & dosen.</p>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] flex flex-wrap gap-2.5 items-center">
        <form action="{{ route('biro.laporan.index') }}" method="GET" class="flex flex-wrap gap-2.5 items-center flex-1">
            <select name="status" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:outline-none">
                <option value="">Semua Status</option>
                <option value="menunggu" {{ $status === 'menunggu' ? 'selected' : '' }}>Menunggu Tinjauan</option>
                <option value="sedang_ditangani" {{ $status === 'sedang_ditangani' ? 'selected' : '' }}>Sedang Ditangani</option>
                <option value="selesai" {{ $status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>

            <select name="urgensi" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:outline-none">
                <option value="">Semua Urgensi</option>
                <option value="darurat" {{ $urgensi === 'darurat' ? 'selected' : '' }}>Darurat</option>
                <option value="tinggi" {{ $urgensi === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                <option value="sedang" {{ $urgensi === 'sedang' ? 'selected' : '' }}>Sedang</option>
                <option value="rendah" {{ $urgensi === 'rendah' ? 'selected' : '' }}>Rendah</option>
            </select>

            <button type="submit" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-lg text-xs transition cursor-pointer shadow-sm">
                Filter
            </button>
        </form>
    </div>

    <!-- Table Wrapped Inside Modern Card -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Seluruh Laporan Masuk</h2>
            <span class="text-xs text-slate-400">Total: {{ $laporanList->total() }} Laporan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50 text-[11px] font-semibold text-slate-600 uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3.5 border-r border-slate-200">Kode & Pelapor</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Ruangan & Fasilitas</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Masalah</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Urgensi</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Status</th>
                        <th class="px-4 py-3.5 text-center">Tindak Lanjut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700 bg-white">
                    @forelse($laporanList as $item)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-4 py-3.5 border-r border-slate-200">
                                <a href="{{ route('biro.laporan.show', $item) }}" class="font-mono font-semibold text-indigo-600 hover:text-indigo-900 hover:underline block">{{ $item->kode_laporan }}</a>
                                <span class="text-slate-700 font-medium block">{{ $item->user->nama_lengkap }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $item->user->nomor_identitas }}</span>
                            </td>

                            <td class="px-4 py-3.5 border-r border-slate-200">
                                <span class="font-medium text-slate-800 block">{{ $item->ruangan->nama_ruangan }}</span>
                                <span class="text-[11px] text-slate-400">Lantai {{ $item->ruangan->lantai }} • {{ $item->ruangan->kode_ruangan }}</span>
                            </td>

                            <td class="px-4 py-3.5 max-w-xs border-r border-slate-200">
                                <span class="font-semibold text-slate-900 block">{{ $item->judul_laporan }}</span>
                                <span class="text-slate-500 line-clamp-1 text-[11px]">{{ $item->deskripsi_masalah }}</span>
                            </td>

                            <td class="px-4 py-3.5 border-r border-slate-200">
                                @if($item->tingkat_urgensi === 'darurat')
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-rose-50 text-rose-700 border border-rose-200">
                                        Darurat
                                    </span>
                                @elseif($item->tingkat_urgensi === 'tinggi')
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">
                                        Tinggi
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ $item->tingkat_urgensi }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 border-r border-slate-200">
                                @if($item->status === 'menunggu')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        Menunggu
                                    </span>
                                @elseif($item->status === 'sedang_ditangani')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                        Ditangani
                                    </span>
                                @elseif($item->status === 'selesai')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                        Ditolak
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a 
                                        href="{{ route('biro.laporan.show', $item) }}" 
                                        class="px-2.5 py-1 text-slate-700 hover:text-slate-900 bg-white hover:bg-slate-100 rounded font-medium text-xs transition border border-slate-200"
                                    >
                                        Detail
                                    </a>
                                    <button 
                                        type="button" 
                                        onclick="openActionModal('{{ $item->id }}', '{{ $item->kode_laporan }}', '{{ $item->status }}')"
                                        class="px-2.5 py-1 text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded font-medium text-xs transition cursor-pointer border border-slate-200"
                                    >
                                        Tindak Lanjut &rarr;
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                                Tidak ada laporan kerusakan tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($laporanList->hasPages())
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50">
                {{ $laporanList->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tindak Lanjut Biro -->
<div id="actionModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-5 shadow-xl border border-slate-100">
        <h3 class="text-sm font-bold text-slate-900 mb-1">Tindak Lanjut Laporan Kerusakan</h3>
        <p class="text-xs text-slate-500 mb-4">Kode Laporan: <span id="modalReportCode" class="font-mono font-semibold text-slate-800"></span></p>

        <form id="actionForm" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="status" class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">
                    Pembaruan Status
                </label>
                <select id="modalStatusSelect" name="status" required class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:outline-none">
                    <option value="menunggu">Menunggu</option>
                    <option value="sedang_ditangani">Sedang Ditangani (Teknisi Dikirim)</option>
                    <option value="selesai">Selesai (Sudah Diperbaiki)</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>

            <div>
                <label for="catatan_tindak_lanjut" class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">
                    Catatan Tindak Lanjut / Alasan
                </label>
                <textarea 
                    name="catatan_tindak_lanjut" 
                    id="catatan_tindak_lanjut" 
                    rows="3" 
                    placeholder="Contoh: Kabel HDMI telah diganti dengan yang baru..." 
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs focus:ring-1 focus:ring-slate-900 focus:outline-none"
                ></textarea>
            </div>

            <div>
                <label for="ubah_status_ruangan" class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">
                    Penyesuaian Status Ruangan (Opsional)
                </label>
                <select name="ubah_status_ruangan" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:outline-none">
                    <option value="">-- Tetap Pertahankan Status Saat Ini --</option>
                    <option value="perawatan">Tutup Sementara (Ubah status ke Perawatan)</option>
                    <option value="aktif">Buka Kembali (Ubah status ke Aktif)</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-2 pt-1">
                <button type="button" onclick="closeActionModal()" class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-lg text-xs transition cursor-pointer shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openActionModal(id, code, currentStatus) {
        document.getElementById('modalReportCode').textContent = code;
        document.getElementById('actionForm').action = `/biro/laporan/${id}/update-status`;
        document.getElementById('modalStatusSelect').value = currentStatus;
        document.getElementById('actionModal').classList.remove('hidden');
    }

    function closeActionModal() {
        document.getElementById('actionModal').classList.add('hidden');
    }
</script>
@endsection
