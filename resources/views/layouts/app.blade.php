<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ruang - Peminjaman Ruangan &amp; Lab Kampus')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{ asset('css/site-theme.css') }}">
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
    </style>
</head>
<body class="site-body min-h-screen flex flex-col antialiased {{ request()->routeIs('login') ? 'site-auth-page' : '' }} {{ request()->routeIs('home') && auth()->guest() ? 'site-public-home' : '' }}">
    <header class="site-header bg-white border-b border-slate-200/80 sticky top-0 z-50 shadow-[0_1px_3px_0_rgba(0,0,0,0.02)]">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-2 xl:gap-4">
                <div class="site-header-leading flex items-center gap-3 xl:gap-5 shrink-0">
                    <button id="mobile-menu-btn" type="button" class="site-menu-toggle inline-flex items-center justify-center w-9 h-9 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition shrink-0" aria-label="Ciutkan sidebar" aria-expanded="true" aria-controls="site-sidebar mobile-menu">
                        <svg id="icon-hamburger" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg id="icon-close" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/></svg>
                    </button>
                    <a href="{{ url('/') }}" class="site-header-brand flex items-center gap-2 shrink-0">
                        <div class="site-brand-mark w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center shadow-sm shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="flex items-baseline gap-1 shrink-0">
                            <span class="text-base font-bold tracking-tight text-slate-900">Ruang</span>
                            <span class="text-[11px] font-medium text-slate-400 hidden sm:inline">Kampus</span>
                        </div>
                    </a>
                    <span class="site-header-context" aria-label="Manajemen Ruangan Kampus">Manajemen Ruangan Kampus</span>
                </div>
                <div class="flex items-center gap-2 xl:gap-3 shrink-0">
                    @guest
                        <a href="{{ route('login') }}" class="hidden sm:block text-xs font-medium text-slate-600 hover:text-slate-900 px-3 py-2 transition">Masuk</a>
                        <a href="{{ route('register') }}" class="hidden sm:block text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 px-3.5 py-2 rounded-lg transition shadow-sm">Daftar Akun</a>
                    @else
                        @php
                            $unreadNotifCount = \App\Models\Notifikasi::untukUser(auth()->user())->belumDibaca()->count();
                            $latestNotifs = \App\Models\Notifikasi::untukUser(auth()->user())->latest()->take(5)->get();
                        @endphp

                        <!-- Notification Bell Dropdown -->
                        <div class="relative" id="notif-dropdown-wrapper">
                            <button id="notif-btn" type="button" class="relative p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition cursor-pointer" aria-label="Notifikasi">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @if($unreadNotifCount > 0)
                                    <span class="absolute top-1 right-1 flex h-4 min-w-4 px-1 items-center justify-center rounded-full bg-rose-600 text-[10px] font-extrabold text-white ring-2 ring-white animate-pulse">
                                        {{ $unreadNotifCount > 99 ? '99+' : $unreadNotifCount }}
                                    </span>
                                @endif
                            </button>

                            <!-- Dropdown Panel Notifikasi -->
                            <div id="notif-panel" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-slate-200/90 z-50 overflow-hidden">
                                <div class="px-4 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-slate-900">Notifikasi</span>
                                        @if($unreadNotifCount > 0)
                                            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">
                                                {{ $unreadNotifCount }} Baru
                                            </span>
                                        @endif
                                    </div>
                                    @if($unreadNotifCount > 0)
                                        <form action="{{ route('notifikasi.mark-all-read') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 transition cursor-pointer">
                                                Tandai Semua Dibaca
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 text-xs">
                                    @forelse($latestNotifs as $notif)
                                        <a href="{{ route('notifikasi.read', $notif) }}" class="block p-3.5 hover:bg-slate-50 transition {{ !$notif->dibaca ? 'bg-indigo-50/40' : '' }}">
                                            <div class="flex items-start gap-2.5">
                                                <span class="mt-0.5 text-base shrink-0">
                                                    @if(str_contains($notif->tipe, 'verifikasi')) 🛡️
                                                    @elseif(str_contains($notif->tipe, 'peminjaman')) 🏢
                                                    @elseif(str_contains($notif->tipe, 'laporan')) 🛠️
                                                    @elseif(str_contains($notif->tipe, 'habis')) ⏰
                                                    @else 🔔
                                                    @endif
                                                </span>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between gap-1 mb-0.5">
                                                        <h4 class="font-bold text-slate-900 truncate text-[11px] {{ !$notif->dibaca ? 'text-indigo-950 font-extrabold' : '' }}">{{ $notif->judul }}</h4>
                                                        <span class="text-[10px] text-slate-400 shrink-0 font-mono">{{ $notif->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <p class="text-[11px] text-slate-600 line-clamp-2 leading-relaxed">{{ $notif->pesan }}</p>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="p-6 text-center text-slate-400">
                                            <p class="text-xs">Belum ada notifikasi.</p>
                                        </div>
                                    @endforelse
                                </div>
                                <div class="p-2.5 bg-slate-50 border-t border-slate-100 text-center">
                                    <a href="{{ route('notifikasi.index') }}" class="text-xs font-semibold text-slate-700 hover:text-slate-900">
                                        Lihat Semua Notifikasi &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>

                        <span class="site-header-divider" aria-hidden="true"></span>
                        <div class="site-header-profile hidden sm:flex items-center gap-2.5">
                            <span class="site-header-avatar" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 21a8 8 0 0 0-16 0m8-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke-width="1.7" stroke-linecap="round"/></svg>
                            </span>
                            <span class="site-header-profile-copy">
                                <span class="site-header-profile-name" title="{{ auth()->user()->nama_lengkap }}">{{ auth()->user()->isBiro() ? 'Biro Administrasi' : auth()->user()->nama_lengkap }}</span>
                                <span class="site-header-profile-role">{{ auth()->user()->isBiro() ? 'Administrator' : ucfirst(auth()->user()->jenis_pengguna) }}</span>
                            </span>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
        <!-- Mobile Menu Panel -->
        <button id="mobile-menu-overlay" class="mobile-menu-overlay" type="button" aria-label="Tutup menu"></button>
        <div id="mobile-menu" class="mobile-menu-panel">
            <div class="max-w-7xl mx-auto px-4 py-3 space-y-0.5">
                @guest
                    <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition {{ request()->routeIs('home') ? 'bg-slate-100 font-semibold' : '' }}">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Beranda
                    </a>
                @endguest
                <a href="{{ route('ruangan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition {{ request()->routeIs('ruangan.*') ? 'bg-slate-100 font-semibold' : '' }}">
                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Katalog Ruangan
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition {{ request()->routeIs('dashboard') ? 'bg-slate-100 font-semibold' : '' }}">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        Dashboard
                    </a>
                    @if(auth()->user()->isBiro())
                        <div class="px-3 pt-3 pb-1"><span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Panel Biro</span></div>
                        <a href="{{ route('biro.ruangan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition {{ request()->routeIs('biro.ruangan.*') ? 'bg-slate-100 font-semibold' : '' }}">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                            Kelola Ruangan
                        </a>
                        <a href="{{ route('biro.jadwal.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition {{ request()->routeIs('biro.jadwal.*') ? 'bg-slate-100 font-semibold' : '' }}">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Jadwal Rutin
                        </a>
                        <a href="{{ route('biro.peminjaman.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition {{ request()->routeIs('biro.peminjaman.*') ? 'bg-slate-100 font-semibold' : '' }}">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Kelola Peminjaman
                        </a>
                        <a href="{{ route('biro.laporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition {{ request()->routeIs('biro.laporan.*') ? 'bg-slate-100 font-semibold' : '' }}">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Laporan Kerusakan
                        </a>
                        <a href="{{ route('biro.verifikasi.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition {{ request()->routeIs('biro.verifikasi.*') ? 'bg-slate-100 font-semibold' : '' }}">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Verifikasi Akun
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}#panduan-peminjaman" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16ZM4 17.5A2.5 2.5 0 0 1 6.5 15H20M8 7h8m-8 4h6"/></svg>
                            Panduan Peminjaman
                        </a>
                        <a href="{{ route('peminjaman.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition {{ request()->routeIs('peminjaman.*') ? 'bg-slate-100 font-semibold' : '' }}">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Peminjaman Saya
                        </a>
                        <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition {{ request()->routeIs('laporan.*') ? 'bg-slate-100 font-semibold' : '' }}">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Laporan Kerusakan
                        </a>
                    @endif
                    <div class="mt-2 pt-3 border-t border-slate-100">
                        <div class="px-3 py-2 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ auth()->user()->isBiro() ? 'Biro Administrasi & Sarana Kampus' : auth()->user()->nama_lengkap }}</p>
                                <p class="text-xs text-slate-400 font-mono">{{ auth()->user()->nomor_identitas }}</p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide {{ auth()->user()->isBiro() ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">{{ auth()->user()->isBiro() ? 'Biro' : auth()->user()->jenis_pengguna }}</span>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="px-3 pb-3">
                            @csrf
                            <button type="submit" class="w-full text-sm font-medium text-rose-600 hover:bg-rose-50 px-3 py-2.5 rounded-lg border border-rose-200 transition cursor-pointer text-center">Keluar dari Akun</button>
                        </form>
                    </div>
                @else
                    <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
                        <a href="{{ route('login') }}" class="text-center text-sm font-medium text-slate-700 px-3 py-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition">Masuk ke Akun</a>
                        <a href="{{ route('register') }}" class="text-center text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 px-3 py-2.5 rounded-lg transition">Daftar Akun Baru</a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <aside id="site-sidebar" class="site-sidebar" aria-label="Navigasi utama">
        <div class="site-sidebar-inner">
            <a href="{{ url('/') }}" class="site-sidebar-brand" aria-label="Ruang Kampus — Beranda">
                <span class="site-brand-mark w-9 h-9 rounded-xl text-white flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4"/></svg>
                </span>
                <span class="site-sidebar-brand-copy"><strong>Ruang <em>Kampus</em></strong><small>Sistem Peminjaman Ruangan</small></span>
            </a>
            <p class="site-sidebar-label">Menu utama</p>
            <nav class="site-sidebar-nav">
                @guest
                    <a href="{{ route('home') }}" title="Beranda" class="site-sidebar-link {{ request()->routeIs('home') ? 'is-active' : '' }}" @if(request()->routeIs('home')) aria-current="page" @endif>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"/></svg><span>Beranda</span>
                    </a>
                    <a href="{{ route('ruangan.index') }}" title="Katalog Ruangan" class="site-sidebar-link {{ request()->routeIs('ruangan.*') ? 'is-active' : '' }}" @if(request()->routeIs('ruangan.*')) aria-current="page" @endif>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 21V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v16M2 21h20M9 7h1m4 0h1M9 11h1m4 0h1M9 21v-5h6v5"/></svg><span>Katalog Ruangan</span>
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" title="Dashboard" class="site-sidebar-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="5" rx="1.5"/><rect x="13" y="10" width="8" height="11" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/></svg><span>Dashboard</span>
                    </a>
                    <a href="{{ route('ruangan.index') }}" title="Katalog Ruangan" class="site-sidebar-link {{ request()->routeIs('ruangan.*') ? 'is-active' : '' }}" @if(request()->routeIs('ruangan.*')) aria-current="page" @endif>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 21V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v16M2 21h20M9 7h1m4 0h1M9 11h1m4 0h1M9 21v-5h6v5"/></svg><span>Katalog Ruangan</span>
                    </a>
                    @if(auth()->user()->isBiro())
                        <p class="site-sidebar-label site-sidebar-label-spaced">Pengelolaan</p>
                        <a href="{{ route('biro.ruangan.index') }}" title="Kelola Ruangan" class="site-sidebar-link {{ request()->routeIs('biro.ruangan.*') ? 'is-active' : '' }}" @if(request()->routeIs('biro.ruangan.*')) aria-current="page" @endif>
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 21h18M5 21V9m14 12V9M3 9h18L12 3 3 9Zm4 4h2m2 0h2m2 0h2"/></svg><span>Kelola Ruangan</span>
                        </a>
                        <a href="{{ route('biro.jadwal.index') }}" title="Jadwal Rutin" class="site-sidebar-link {{ request()->routeIs('biro.jadwal.*') ? 'is-active' : '' }}" @if(request()->routeIs('biro.jadwal.*')) aria-current="page" @endif>
                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18M8 14h3m-3 3h7"/></svg><span>Jadwal Rutin</span>
                        </a>
                        <a href="{{ route('biro.peminjaman.index') }}" title="Kelola Peminjaman" class="site-sidebar-link {{ request()->routeIs('biro.peminjaman.*') ? 'is-active' : '' }}" @if(request()->routeIs('biro.peminjaman.*')) aria-current="page" @endif>
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 4H6a2 2 0 0 0-2 2v14h16V6a2 2 0 0 0-2-2h-2M9 4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v3H9zM8 12h8m-8 4h8"/></svg><span>Kelola Peminjaman</span>
                        </a>
                        <a href="{{ route('biro.laporan.index') }}" title="Laporan Kerusakan" class="site-sidebar-link {{ request()->routeIs('biro.laporan.*') ? 'is-active' : '' }}" @if(request()->routeIs('biro.laporan.*')) aria-current="page" @endif>
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 2.8 19a1.3 1.3 0 0 0 1.1 2h16.2a1.3 1.3 0 0 0 1.1-2L12 3Z"/><path d="M12 9v4m0 3h.01"/></svg><span>Laporan Kerusakan</span>
                        </a>
                        <a href="{{ route('biro.verifikasi.index') }}" title="Verifikasi Akun" class="site-sidebar-link {{ request()->routeIs('biro.verifikasi.*') ? 'is-active' : '' }}" @if(request()->routeIs('biro.verifikasi.*')) aria-current="page" @endif>
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z"/><path d="m9 12 2 2 4-4"/></svg><span>Verifikasi Akun</span>
                        </a>
                    @else
                        <p class="site-sidebar-label site-sidebar-label-spaced">Aktivitas saya</p>
                        <a href="{{ route('dashboard') }}#panduan-peminjaman" title="Panduan Peminjaman" class="site-sidebar-link">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16Z"/><path d="M4 17.5A2.5 2.5 0 0 1 6.5 15H20M8 7h8m-8 4h6"/></svg><span>Panduan Peminjaman</span>
                        </a>
                        <a href="{{ route('peminjaman.index') }}" title="Peminjaman Saya" class="site-sidebar-link {{ request()->routeIs('peminjaman.*') ? 'is-active' : '' }}" @if(request()->routeIs('peminjaman.*')) aria-current="page" @endif>
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 4H6a2 2 0 0 0-2 2v14h16V6a2 2 0 0 0-2-2h-2M9 4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v3H9zM8 12h8m-8 4h8"/></svg><span>Peminjaman Saya</span>
                        </a>
                        <a href="{{ route('laporan.index') }}" title="Laporan Kerusakan" class="site-sidebar-link {{ request()->routeIs('laporan.*') ? 'is-active' : '' }}" @if(request()->routeIs('laporan.*')) aria-current="page" @endif>
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 2.8 19a1.3 1.3 0 0 0 1.1 2h16.2a1.3 1.3 0 0 0 1.1-2L12 3Z"/><path d="M12 9v4m0 3h.01"/></svg><span>Laporan Kerusakan</span>
                        </a>
                    @endif
                @endauth
            </nav>

            @guest
                <div class="site-sidebar-note">
                    <span class="site-sidebar-note-icon" aria-hidden="true">i</span>
                    <p>Temukan ruang kampus dan cek ketersediaannya dari katalog.</p>
                </div>
            @else
                <div class="site-sidebar-account">
                    <div class="site-sidebar-avatar" title="{{ auth()->user()->nama_lengkap }}" aria-hidden="true">{{ strtoupper(substr(auth()->user()->nama_lengkap, 0, 1)) }}</div>
                    <div class="min-w-0">
                        <p class="site-sidebar-account-name">{{ auth()->user()->isBiro() ? 'Biro Administrasi & Sarana Kampus' : auth()->user()->nama_lengkap }}</p>
                        <p class="site-sidebar-account-role">{{ auth()->user()->isBiro() ? 'Biro Pengelola' : ucfirst(auth()->user()->jenis_pengguna) }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="site-sidebar-logout">
                        @csrf
                        <button type="submit" title="Keluar dari akun" aria-label="Keluar dari akun">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17l5-5-5-5m5 5H3m9-9h5a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3h-5"/></svg>
                        </button>
                    </form>
                </div>
            @endguest
        </div>
    </aside>

    <main class="site-main flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if(session('info') || session('status'))
            <div class="mb-5 p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 flex items-center gap-2.5 text-xs font-medium shadow-sm">
                <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('info') ?? session('status') }}</span>
            </div>
        @endif
        @if(session('success'))
            <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-2.5 text-xs font-medium shadow-sm">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-2.5 text-xs font-medium shadow-sm">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if(session('warning'))
            <div class="mb-5 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center gap-2.5 text-xs font-medium shadow-sm">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif
        @yield('content')
    </main>

    <footer class="site-footer bg-white border-t border-slate-200/80 py-5 text-center text-xs text-slate-400 mt-auto">
        <p>&copy; {{ date('Y') }} EduSpace &bull; Sistem Informasi Peminjaman Ruangan &amp; Lab Kampus</p>
    </footer>

    <script>
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
        const iconHamburger = document.getElementById('icon-hamburger');
        const iconClose = document.getElementById('icon-close');
        const desktopSidebarQuery = window.matchMedia('(min-width: 1024px)');

        try {
            if (window.localStorage.getItem('ruang-sidebar-collapsed') === 'true') {
                document.body.classList.add('sidebar-collapsed');
            }
        } catch (error) {
            // Sidebar remains usable when browser storage is unavailable.
        }

        function syncSidebarToggle() {
            const isDesktop = desktopSidebarQuery.matches;
            const isExpanded = isDesktop
                ? !document.body.classList.contains('sidebar-collapsed')
                : document.body.classList.contains('mobile-nav-open');

            mobileBtn.setAttribute('aria-expanded', String(isExpanded));
            mobileBtn.setAttribute('aria-label', isDesktop
                ? (isExpanded ? 'Ciutkan sidebar' : 'Perluas sidebar')
                : (isExpanded ? 'Tutup menu' : 'Buka menu'));
            const showCloseIcon = !isDesktop && isExpanded;
            iconHamburger.classList.toggle('hidden', showCloseIcon);
            iconClose.classList.toggle('hidden', !showCloseIcon);
        }

        function closeMobileMenu() {
            document.body.classList.remove('mobile-nav-open');
            syncSidebarToggle();
        }

        mobileBtn.addEventListener('click', function (event) {
            event.stopPropagation();
            if (desktopSidebarQuery.matches) {
                document.body.classList.toggle('sidebar-collapsed');
                try {
                    window.localStorage.setItem('ruang-sidebar-collapsed', String(document.body.classList.contains('sidebar-collapsed')));
                } catch (error) {
                    // The current page still updates if browser storage is unavailable.
                }
            } else {
                document.body.classList.toggle('mobile-nav-open');
            }
            syncSidebarToggle();
        });

        mobileMenuOverlay.addEventListener('click', closeMobileMenu);
        document.querySelectorAll('#mobile-menu a').forEach(function (link) {
            link.addEventListener('click', closeMobileMenu);
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && document.body.classList.contains('mobile-nav-open')) {
                closeMobileMenu();
            }
        });
        window.addEventListener('resize', function () {
            if (desktopSidebarQuery.matches) closeMobileMenu();
            syncSidebarToggle();
        });
        syncSidebarToggle();

        const notifBtn   = document.getElementById('notif-btn');
        const notifPanel = document.getElementById('notif-panel');
        if (notifBtn && notifPanel) {
            notifBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                notifPanel.classList.toggle('hidden');
            });
            document.addEventListener('click', function(e) {
                if (!notifPanel.contains(e.target) && !notifBtn.contains(e.target)) {
                    notifPanel.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>
