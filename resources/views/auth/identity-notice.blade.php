@extends('layouts.app')

@section('title', 'Status Verifikasi Identitas - EduSpace')

@section('content')
<div class="max-w-xl mx-auto my-12">
    <div class="bg-white p-8 rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100">
        
        @if($user->status_verifikasi === 'menunggu')
            <div class="text-center">
                <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-slate-900 mb-2">Identitas Kampus Sedang Ditinjau</h1>
                <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                    Data akun Anda sedang dalam proses verifikasi oleh pihak <strong>Biro Pengelola Kampus</strong>. Kami memastikan identitas Anda cocok dengan data akademik resmi.
                </p>

                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-left mb-6 space-y-2 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500">Nama Lengkap:</span>
                        <span class="font-bold text-slate-800">{{ $user->nama_lengkap }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500">Jenis Pengguna:</span>
                        <span class="font-bold text-slate-800 uppercase">{{ $user->jenis_pengguna }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500">Nomor Identitas:</span>
                        <span class="font-bold text-indigo-600 font-mono">{{ $user->nomor_identitas }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-500">Status Saat Ini:</span>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                            Menunggu Verifikasi Manual
                        </span>
                    </div>
                </div>

                <a href="{{ route('dashboard') }}" class="inline-block py-2.5 px-6 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-xs transition">
                    Kembali ke Dashboard
                </a>
            </div>

        @elseif($user->status_verifikasi === 'ditolak')
            <div class="text-center">
                <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-rose-600 mb-2">Verifikasi Identitas Ditolak</h1>
                <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                    Mohon maaf, permohonan verifikasi identitas kampus Anda tidak disetujui oleh pihak Biro Kampus.
                </p>

                <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 text-left mb-6">
                    <h3 class="text-xs font-bold text-rose-800 uppercase tracking-wider mb-1">Alasan Penolakan:</h3>
                    <p class="text-xs text-rose-700 leading-relaxed">{{ $user->alasan_penolakan ?? 'Data identitas kampus tidak sesuai dengan arsip pangkalan data kampus.' }}</p>
                </div>

                <p class="text-xs text-slate-500 mb-6">
                    Silakan hubungi loket Biro Administrasi Kampus secara langsung dengan membawa kartu identitas untuk pembaruan data akun Anda.
                </p>

                <a href="{{ route('dashboard') }}" class="inline-block py-2.5 px-6 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-xl text-xs transition">
                    Kembali ke Dashboard
                </a>
            </div>
        @else
            <div class="text-center">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-emerald-700 mb-2">Identitas Terverifikasi</h1>
                <p class="text-sm text-slate-600 mb-6">
                    Identitas kampus Anda telah aktif dan terverifikasi secara resmi. Anda dapat langsung menggunakan fasilitas peminjaman ruangan.
                </p>
                <a href="{{ route('dashboard') }}" class="inline-block py-2.5 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs transition">
                    Buka Dashboard
                </a>
            </div>
        @endif

    </div>
</div>
@endsection
