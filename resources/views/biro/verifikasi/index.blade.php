@extends('layouts.app')

@section('title', 'Verifikasi Akun Pengguna - Biro Kampus')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Verifikasi Identitas Kampus</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pemeriksaan manual data mahasiswa (NPM) dan dosen (NIDN/NIP) sebelum dapat meminjam ruangan.</p>
        </div>

        <!-- Filter Status Tab -->
        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-lg text-xs font-medium self-start sm:self-auto">
            <a href="{{ route('biro.verifikasi.index', ['status' => 'menunggu']) }}" class="px-3 py-1.5 rounded-md transition {{ $status === 'menunggu' ? 'bg-white text-slate-900 shadow-sm font-semibold' : 'text-slate-600 hover:text-slate-900' }}">
                Menunggu ({{ $counts['menunggu'] }})
            </a>
            <a href="{{ route('biro.verifikasi.index', ['status' => 'terverifikasi']) }}" class="px-3 py-1.5 rounded-md transition {{ $status === 'terverifikasi' ? 'bg-white text-slate-900 shadow-sm font-semibold' : 'text-slate-600 hover:text-slate-900' }}">
                Disetujui ({{ $counts['terverifikasi'] }})
            </a>
            <a href="{{ route('biro.verifikasi.index', ['status' => 'ditolak']) }}" class="px-3 py-1.5 rounded-md transition {{ $status === 'ditolak' ? 'bg-white text-slate-900 shadow-sm font-semibold' : 'text-slate-600 hover:text-slate-900' }}">
                Ditolak ({{ $counts['ditolak'] }})
            </a>
        </div>
    </div>

    <!-- Search Box Card -->
    <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)]">
        <form action="{{ route('biro.verifikasi.index') }}" method="GET" class="flex gap-2">
            <input type="hidden" name="status" value="{{ $status }}">
            <input 
                type="text" 
                name="q" 
                value="{{ $search }}" 
                placeholder="Cari berdasarkan nama lengkap, NPM / NIDN, atau email..." 
                class="flex-1 px-3.5 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-1 focus:ring-slate-900 focus:outline-none"
            >
            <button type="submit" class="px-4 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-lg text-xs transition cursor-pointer shadow-sm">
                Cari Data
            </button>
            @if($search)
                <a href="{{ route('biro.verifikasi.index', ['status' => $status]) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium rounded-lg text-xs transition flex items-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table Wrapped Inside Modern Card -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-[0_1px_2px_0_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Daftar Akun Pengguna</h2>
            <span class="text-xs text-slate-400">Total: {{ $users->total() }} Pengguna</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50 text-[11px] font-semibold text-slate-600 uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3.5 border-r border-slate-200">Pengguna</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Identitas Kampus</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Verifikasi Email</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Status Akun</th>
                        <th class="px-4 py-3.5 border-r border-slate-200">Status Identitas</th>
                        <th class="px-4 py-3.5 text-center">Tindak Lanjut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700 bg-white">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Profil -->
                            <td class="px-4 py-3.5 border-r border-slate-200">
                                <span class="font-semibold text-slate-900 block">{{ $user->nama_lengkap }}</span>
                                <span class="text-[11px] text-slate-400">{{ $user->email }}</span>
                            </td>

                            <!-- Identitas -->
                            <td class="px-4 py-3.5 border-r border-slate-200">
                                <span class="font-mono font-semibold text-slate-800 block">{{ $user->nomor_identitas }}</span>
                                <span class="text-[10px] uppercase font-bold text-slate-500">{{ $user->jenis_pengguna }}</span>
                            </td>

                            <!-- Status Email -->
                            <td class="px-4 py-3.5 border-r border-slate-200">
                                @if($user->hasVerifiedEmail())
                                    <span class="inline-flex items-center gap-1 text-emerald-700 font-medium">
                                        ✓ Terverifikasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-amber-600 font-medium">
                                        Belum Verifikasi
                                    </span>
                                @endif
                            </td>

                            <!-- Status Akun -->
                            <td class="px-4 py-3.5 border-r border-slate-200">
                                <form action="{{ route('biro.verifikasi.toggle-active', $user) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="cursor-pointer" title="Klik untuk mengubah status aktif/nonaktif">
                                        @if($user->is_active)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                ● Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                                ● Nonaktif
                                            </span>
                                        @endif
                                    </button>
                                </form>
                            </td>

                            <!-- Status Identitas -->
                            <td class="px-4 py-3.5 border-r border-slate-200">
                                @if($user->status_verifikasi === 'terverifikasi')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Disetujui
                                    </span>
                                @elseif($user->status_verifikasi === 'ditolak')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        Menunggu Tinjauan
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="px-4 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($user->status_verifikasi !== 'terverifikasi')
                                        <form action="{{ route('biro.verifikasi.approve', $user) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button 
                                                type="submit" 
                                                onclick="return confirm('Apakah Anda yakin data identitas {{ $user->nama_lengkap }} valid dan disetujui?')"
                                                class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded font-medium text-xs transition cursor-pointer shadow-xs"
                                            >
                                                Setujui
                                            </button>
                                        </form>
                                    @endif

                                    @if($user->status_verifikasi !== 'ditolak')
                                        <button 
                                            type="button" 
                                            onclick="openRejectModal('{{ $user->id }}', '{{ $user->nama_lengkap }}')"
                                            class="px-2.5 py-1 text-rose-600 hover:bg-rose-50 border border-rose-200 rounded font-medium text-xs transition cursor-pointer"
                                        >
                                            Tolak
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                                Tidak ada data pengguna dengan status <strong>{{ $status }}</strong>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tolak Verifikasi -->
<div id="rejectModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-5 shadow-xl border border-slate-100">
        <h3 class="text-sm font-bold text-slate-900 mb-1">Tolak Verifikasi Pengguna</h3>
        <p class="text-xs text-slate-500 mb-4">Pengguna: <strong id="modalUserName" class="text-slate-800"></strong></p>

        <form id="rejectForm" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="alasan_penolakan" class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">
                    Alasan Penolakan (Wajib Diisi)
                </label>
                <textarea 
                    name="alasan_penolakan" 
                    id="alasan_penolakan" 
                    rows="3" 
                    required 
                    placeholder="Contoh: Nomor NPM tidak terdaftar di sistem akademik kampus..."
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs focus:ring-1 focus:ring-rose-500 focus:outline-none"
                ></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-1">
                <button 
                    type="button" 
                    onclick="closeRejectModal()"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition cursor-pointer"
                >
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded-lg text-xs transition cursor-pointer shadow-sm"
                >
                    Tolak Verifikasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(userId, userName) {
        document.getElementById('modalUserName').textContent = userName;
        document.getElementById('rejectForm').action = `/biro/verifikasi-pengguna/${userId}/reject`;
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('alasan_penolakan').value = '';
    }
</script>
@endsection
