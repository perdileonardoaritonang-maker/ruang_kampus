@extends('layouts.app')

@section('title', 'Verifikasi Email - EduSpace')

@section('content')
<div class="max-w-md mx-auto my-12">
    <div class="bg-white p-8 rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 text-center">
        <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>

        <h1 class="text-xl font-bold text-slate-900 mb-2">Verifikasi Alamat Email Anda</h1>
        <p class="text-sm text-slate-600 mb-6 leading-relaxed">
            Terima kasih telah mendaftar! Sebelum meminjam ruangan, silakan klik tautan verifikasi yang baru saja kami kirimkan ke email Anda:
            <strong class="text-slate-800 block mt-1">{{ auth()->user()->email }}</strong>
        </p>

        @if (session('message'))
            <div class="mb-6 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-xl font-medium">
                {{ session('message') }}
            </div>
        @endif

        <div class="space-y-3">
            <form action="{{ route('verification.send') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-xs transition cursor-pointer">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>

            <a href="{{ route('dashboard') }}" class="block w-full py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition">
                Lanjut ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
