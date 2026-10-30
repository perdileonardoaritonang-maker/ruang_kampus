@extends('layouts.app')

@section('title', 'Pusat Notifikasi - EduSpace')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <span>Pusat Notifikasi</span>
                @php
                    $unreadCount = $notifikasiList->where('dibaca', false)->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700">
                        {{ $unreadCount }} Belum Dibaca
                    </span>
                @endif
            </h1>
            <p class="text-xs text-slate-500 mt-1">Pantau pembaruan status akun, pengajuan peminjaman, tanggapan laporan kerusakan, dan peringatan sistem.</p>
        </div>

        @if($notifikasiList->total() > 0)
            <form action="{{ route('notifikasi.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-lg border border-slate-300 transition shadow-xs cursor-pointer">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Tandai Semua Telah Dibaca
                </button>
            </form>
        @endif
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_1px_3px_0_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="divide-y divide-slate-100">
            @forelse($notifikasiList as $notif)
                <div class="p-4 sm:p-5 transition hover:bg-slate-50/70 {{ !$notif->dibaca ? 'bg-indigo-50/30' : '' }}">
                    <div class="flex items-start gap-3.5">
                        <!-- Icon Avatar -->
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 {{ !$notif->dibaca ? 'bg-indigo-100/80 shadow-xs' : 'bg-slate-100' }}">
                            @if(str_contains($notif->tipe, 'verifikasi'))
                                🛡️
                            @elseif(str_contains($notif->tipe, 'peminjaman'))
                                🏢
                            @elseif(str_contains($notif->tipe, 'laporan'))
                                🛠️
                            @elseif(str_contains($notif->tipe, 'habis'))
                                ⏰
                            @elseif(str_contains($notif->tipe, 'batal'))
                                ❌
                            @else
                                🔔
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-xs sm:text-sm font-bold text-slate-900 {{ !$notif->dibaca ? 'text-indigo-950 font-extrabold' : '' }}">
                                        {{ $notif->judul }}
                                    </h3>
                                    @if(!$notif->dibaca)
                                        <span class="inline-block w-2 h-2 rounded-full bg-rose-600 ring-2 ring-rose-200" title="Belum dibaca"></span>
                                    @endif
                                </div>
                                <span class="text-[11px] text-slate-400 font-mono shrink-0" title="{{ $notif->created_at->format('d M Y H:i:s') }}">
                                    {{ $notif->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-600 leading-relaxed mb-3">
                                {{ $notif->pesan }}
                            </p>

                            <!-- Actions -->
                            <div class="flex items-center gap-3">
                                @if($notif->url)
                                    <a href="{{ route('notifikasi.read', $notif) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                                        Lihat Rincian
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                @endif

                                @if(!$notif->dibaca)
                                    <a href="{{ route('notifikasi.read', $notif) }}" class="text-[11px] font-medium text-slate-400 hover:text-slate-600 transition">
                                        Tandai Dibaca
                                    </a>
                                @else
                                    <span class="text-[11px] text-slate-400 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Sudah dibaca
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 flex items-center justify-center text-2xl text-slate-400 mb-3">
                        📭
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">Belum Ada Notifikasi</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        Semua pemberitahuan penting terkait verifikasi, jadwal peminjaman, dan tanggapan laporan Anda akan ditampilkan di sini.
                    </p>
                </div>
            @endforelse
        </div>

        @if($notifikasiList->hasPages())
            <div class="p-4 bg-slate-50 border-t border-slate-100">
                {{ $notifikasiList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
