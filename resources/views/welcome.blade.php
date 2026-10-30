@extends('layouts.app')

@section('title', 'Ruang Kampus — Peminjaman Ruangan dan Laboratorium')

@section('content')
@php
    $quickWeekStart = \Carbon\Carbon::now()->startOfWeek();
    $quickWeekEnd = $quickWeekStart->copy()->addDays(6);
    $quickDayNames = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
@endphp
<div class="public-home">
    <header class="public-home-header">
        <div class="public-home-header-inner">
            <a href="{{ route('home') }}" class="public-home-brand" aria-label="Ruang Kampus, beranda">
                <span class="public-home-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                <span><strong>Ruang <em>Kampus</em></strong><small>Sistem Peminjaman Ruangan</small></span>
            </a>
            <button class="public-home-menu-toggle" type="button" aria-label="Buka navigasi" aria-expanded="false" aria-controls="public-home-nav" data-home-menu-toggle>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
            <nav class="public-home-nav" id="public-home-nav" aria-label="Navigasi utama" data-home-nav>
                <a href="#fitur">Fitur Utama</a>
                <a href="#cek-jadwal">Cek Jadwal</a>
                <a href="#panduan">Panduan</a>
                <a href="#lapor-fasilitas">Lapor Fasilitas</a>
            </nav>
            <a href="{{ route('login') }}" class="public-home-login">Masuk Akun <span aria-hidden="true">→</span></a>
        </div>
    </header>

    <main>
        <section class="public-home-hero" id="cek-jadwal" aria-labelledby="public-hero-title">
            <img class="public-home-building-bg" src="{{ asset('images/campus-hero.svg') }}" alt="" aria-hidden="true">
            <div class="public-home-hero-inner">
                <div class="public-home-hero-copy">
                    <span class="public-home-eyebrow"><i></i> SISTEM FASILITAS KAMPUS</span>
                    <h1 id="public-hero-title">Kelola &amp; Pinjam Ruangan Kampus Lebih Cepat &amp; Transparan</h1>
                    <p>Temukan ruang kuliah dan laboratorium, cek jadwalnya, lalu ajukan peminjaman dalam satu sistem yang mudah digunakan.</p>
                    <div class="public-home-hero-actions">
                        <a href="{{ route('ruangan.index') }}" class="public-button-primary">Jelajahi Ruangan <span aria-hidden="true">→</span></a>
                        <a href="#panduan" class="public-button-secondary">Lihat Panduan</a>
                    </div>
                    <div class="public-home-trust"><span aria-hidden="true">✓</span> Informasi fasilitas terpusat dan mudah diakses</div>
                </div>

                <form class="public-quick-search" action="{{ route('ruangan.index') }}" method="GET" id="public-availability-search">
                    <div class="public-search-heading">
                        <span class="public-search-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg></span>
                        <span><strong>Cek ketersediaan ruangan</strong><small>Pilih lantai dan tanggal untuk melihat jadwal.</small></span>
                    </div>
                    <label for="quick-floor">Lantai gedung</label>
                    <div class="public-search-control">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-5h6v5M8 9h.01M12 9h.01M16 9h.01M8 12h.01M12 12h.01M16 12h.01"/></svg>
                        <input id="quick-floor" type="number" name="lantai" min="1" max="255" step="1" inputmode="numeric" placeholder="Semua lantai">
                    </div>
                    <label for="quick-date">Tanggal</label>
                    <div class="public-search-control">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
                        <input id="quick-date" type="date" name="tanggal" value="{{ \Carbon\Carbon::today()->toDateString() }}" min="{{ $quickWeekStart->toDateString() }}" max="{{ $quickWeekEnd->toDateString() }}" required>
                    </div>
                    <input type="hidden" name="hari" id="quick-day" value="{{ $quickDayNames[\Carbon\Carbon::today()->dayOfWeek] }}">
                    <button type="submit" class="public-search-submit">Cek Jadwal Ruangan <span aria-hidden="true">→</span></button>
                    <p class="public-search-note">Jadwal yang ditampilkan menyesuaikan hari pada minggu ini.</p>
                </form>
            </div>
            <div class="public-hero-decoration public-hero-decoration-one" aria-hidden="true"></div>
            <div class="public-hero-decoration public-hero-decoration-two" aria-hidden="true"></div>
        </section>

        <section class="public-home-section public-features" id="fitur" aria-labelledby="features-title">
            <div class="public-section-heading">
                <span class="public-home-eyebrow">LAYANAN RUANG KAMPUS</span>
                <h2 id="features-title">Semua kebutuhan fasilitas dalam satu tempat</h2>
                <p>Mulai dari mencari ruangan hingga menyampaikan laporan fasilitas.</p>
            </div>
            <div class="public-feature-grid">
                <a class="public-feature-card" href="{{ route('ruangan.index') }}">
                    <span class="public-feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18m9 3v5m-2.5-2.5h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <h3>Peminjaman Ruangan &amp; Lab</h3>
                    <p>Ajukan ruang kuliah atau laboratorium sesuai kebutuhan kegiatan kampus.</p>
                    <span class="public-feature-link">Lihat ruangan <b aria-hidden="true">→</b></span>
                </a>
                <a class="public-feature-card" href="#cek-jadwal">
                    <span class="public-feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <h3>Cek Ketersediaan Real-Time</h3>
                    <p>Periksa jadwal, kapasitas, dan fasilitas ruangan sebelum mengajukan.</p>
                    <span class="public-feature-link">Cek jadwal <b aria-hidden="true">→</b></span>
                </a>
                <a class="public-feature-card" id="lapor-fasilitas" href="{{ route('login') }}">
                    <span class="public-feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z"/><path d="M12 8v4m0 4h.01" stroke-width="1.8" stroke-linecap="round"/></svg></span>
                    <h3>Laporan Kerusakan Fasilitas</h3>
                    <p>Sampaikan laporan AC, proyektor, komputer, Wi-Fi, atau sarana lainnya.</p>
                    <span class="public-feature-link">Masuk untuk melapor <b aria-hidden="true">→</b></span>
                </a>
                <a class="public-feature-card" href="#panduan">
                    <span class="public-feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16Z"/><path d="M4 17.5A2.5 2.5 0 0 1 6.5 15H20M8 7h8m-8 4h6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <h3>Informasi &amp; Panduan</h3>
                    <p>Ikuti alur penggunaan layanan dan ketentuan peminjaman ruangan.</p>
                    <span class="public-feature-link">Pelajari alurnya <b aria-hidden="true">→</b></span>
                </a>
            </div>
        </section>

        <section class="public-guide" id="panduan" aria-labelledby="guide-title">
            <div class="public-guide-copy">
                <span class="public-home-eyebrow">MULAI DENGAN MUDAH</span>
                <h2 id="guide-title">Alur peminjaman yang jelas dari awal</h2>
                <p>Gunakan akun kampus Anda untuk mengajukan peminjaman. Status dan informasi proses dapat dipantau melalui sistem.</p>
            </div>
            <ol class="public-guide-steps">
                <li><span>01</span><div><strong>Masuk atau daftar</strong><small>Gunakan identitas mahasiswa atau dosen.</small></div></li>
                <li><span>02</span><div><strong>Pilih ruangan dan jadwal</strong><small>Periksa ketersediaan sebelum mengajukan.</small></div></li>
                <li><span>03</span><div><strong>Kirim pengajuan</strong><small>Lengkapi kebutuhan dan tunggu proses persetujuan.</small></div></li>
            </ol>
        </section>
    </main>

    <footer class="public-home-footer">
        <a href="{{ route('home') }}" class="public-home-brand" aria-label="Ruang Kampus, beranda">
            <span class="public-home-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <span><strong>Ruang <em>Kampus</em></strong><small>Sistem Peminjaman Ruangan</small></span>
        </a>
        <p>&copy; {{ date('Y') }} Ruang Kampus. Sistem Informasi Peminjaman Ruangan dan Laboratorium Kampus.</p>
    </footer>
</div>

<script>
    (function () {
        const menuToggle = document.querySelector('[data-home-menu-toggle]');
        const nav = document.querySelector('[data-home-nav]');
        const searchForm = document.getElementById('public-availability-search');
        const dateInput = document.getElementById('quick-date');
        const dayInput = document.getElementById('quick-day');
        const dayNames = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

        if (menuToggle && nav) {
            menuToggle.addEventListener('click', function () {
                const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
                menuToggle.setAttribute('aria-expanded', String(!isOpen));
                menuToggle.setAttribute('aria-label', isOpen ? 'Buka navigasi' : 'Tutup navigasi');
                nav.classList.toggle('is-open', !isOpen);
            });
            nav.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    nav.classList.remove('is-open');
                    menuToggle.setAttribute('aria-expanded', 'false');
                    menuToggle.setAttribute('aria-label', 'Buka navigasi');
                });
            });
        }

        if (searchForm && dateInput && dayInput) {
            searchForm.addEventListener('submit', function (event) {
                if (!dateInput.value) {
                    event.preventDefault();
                    dateInput.focus();
                    return;
                }
                const selectedDate = new Date(dateInput.value + 'T00:00:00');
                dayInput.value = dayNames[selectedDate.getDay()];
            });
        }
    }());
</script>
@endsection
