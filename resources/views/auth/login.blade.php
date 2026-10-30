@extends('layouts.app')

@section('title', 'Masuk — Ruang Kampus')

@section('content')
@php
    $showRegisterMode = $errors->has('nama_lengkap') || $errors->has('jenis_pengguna') || $errors->has('nomor_identitas') || old('jenis_pengguna') !== null;
@endphp
<div class="login-page">
    <section class="login-intro" aria-labelledby="login-intro-title">
        <a href="{{ route('home') }}" class="login-brand" aria-label="Ruang Kampus, beranda">
            <span class="login-brand-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span class="login-brand-copy"><strong>Ruang <em>Kampus</em></strong><small>Sistem Informasi Peminjaman Ruangan dan Laboratorium</small></span>
        </a>

        <div class="login-intro-copy">
            <span class="login-eyebrow"><span></span> LAYANAN FASILITAS KAMPUS</span>
            <h1 id="login-intro-title">Peminjaman Ruangan dan Laboratorium Kampus Kini Lebih Mudah</h1>
            <p>Ruang Kampus memudahkan mahasiswa dan dosen mencari ketersediaan, mengajukan peminjaman ruangan dan laboratorium, serta melaporkan kerusakan fasilitas secara terintegrasi.</p>

            <div class="login-benefits">
                <article class="login-benefit">
                    <span class="login-benefit-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18m-9 3v5m-2.5-2.5h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <span><strong>Peminjaman Ruangan dan Lab</strong><small>Ajukan peminjaman sesuai jadwal dan ketersediaan.</small></span>
                </article>
                <article class="login-benefit">
                    <span class="login-benefit-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-5h6v5M8 9h.01M12 9h.01M16 9h.01M8 12h.01M12 12h.01M16 12h.01" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <span><strong>Informasi Ketersediaan</strong><small>Lihat ruangan, kapasitas, laboratorium, dan fasilitasnya.</small></span>
                </article>
                <article class="login-benefit">
                    <span class="login-benefit-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z" stroke-width="1.8" stroke-linejoin="round"/><path d="m9 12 2 2 4-4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <span><strong>Laporan Fasilitas</strong><small>Laporkan kerusakan AC, proyektor, komputer, Wi-Fi, dan lainnya.</small></span>
                </article>
            </div>
        </div>

        <div class="login-campus-art" aria-hidden="true">
            <svg viewBox="0 0 820 290" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 252c124-35 210-25 321 2 135 33 260 13 499-23v59H0v-38Z" fill="#DBEAFE"/>
                <path d="M0 269c175-28 314-10 444 3 137 13 252-3 376-27v45H0v-21Z" fill="#BFDBFE" opacity=".7"/>
                <path d="M93 245V105l152-48 152 48v140H93Z" fill="#fff" stroke="#B8D3F5" stroke-width="3"/>
                <path d="M78 108 245 49l166 59H78Z" fill="#DCEBFF" stroke="#B8D3F5" stroke-width="3"/>
                <path d="M120 119h42v39h-42zm65 0h42v39h-42zm65 0h42v39h-42zm65 0h42v39h-42zM120 177h42v39h-42zm65 0h42v39h-42zm130 0h42v39h-42z" fill="#C7E0FF" stroke="#9FC4F2" stroke-width="2"/>
                <path d="M250 177h42v68h-42z" fill="#93BDF1"/><path d="M235 177h72v8h-72z" fill="#B7D3F8"/>
                <path d="M432 247V145h317v102" fill="#F8FBFF" stroke="#B8D3F5" stroke-width="3"/>
                <path d="M416 146h347l-19-23H438l-22 23Z" fill="#DCEBFF" stroke="#B8D3F5" stroke-width="3"/>
                <path d="M460 161h64v63h-64zm85 0h64v63h-64zm85 0h64v63h-64z" fill="#D5E8FF" stroke="#A9C9F3" stroke-width="3"/>
                <path d="M575 177h91v55h-91z" fill="#fff" stroke="#A9C9F3" stroke-width="3"/><path d="M586 188h69v34h-69z" fill="#E8F2FF"/>
                <path d="M400 247h380" stroke="#9DBDE8" stroke-width="3" stroke-linecap="round"/>
                <path d="M516 247v-23m142 23v-23" stroke="#9DBDE8" stroke-width="4"/>
                <path d="M524 247h-17m159 0h-17" stroke="#9DBDE8" stroke-width="4" stroke-linecap="round"/>
                <circle cx="56" cy="220" r="24" fill="#BFDBFE"/><path d="M56 245v-54m0 21-18-18m18 28 20-20" stroke="#7FA9DE" stroke-width="5" stroke-linecap="round"/>
                <circle cx="783" cy="221" r="21" fill="#BFDBFE"/><path d="M783 244v-46m0 17-15-14m15 24 17-17" stroke="#7FA9DE" stroke-width="5" stroke-linecap="round"/>
            </svg>
        </div>
        <span class="login-intro-footnote">Satu platform untuk fasilitas kampus yang lebih mudah diakses.</span>
    </section>

    <section class="login-form-side" aria-labelledby="login-title">
        <div class="login-form-wrap">
            <a href="{{ route('home') }}" class="login-form-brand" aria-label="Ruang Kampus, beranda">
                <span class="login-brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                <span class="login-brand-copy"><strong>Ruang <em>Kampus</em></strong><small>Sistem Peminjaman Ruangan</small></span>
            </a>

            <div class="login-panel login-panel-login {{ $showRegisterMode ? '' : 'is-active' }}" data-auth-panel="login" @if($showRegisterMode) hidden @endif>
            <div class="login-form-heading">
                <span class="login-form-kicker">AKSES AKUN</span>
                <h2 id="login-title">Selamat Datang</h2>
                <p>Silakan masuk ke akun Anda untuk melanjutkan.</p>
            </div>

            @if($errors->any())
                <div class="login-errors" role="alert">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="login-form">
                @csrf
                <div class="login-field">
                    <label for="email">Email</label>
                    <div class="login-input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@kampus.ac.id">
                    </div>
                </div>
                <div class="login-field">
                    <label for="password">Kata Sandi</label>
                    <div class="login-input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3m-4 5v2"/></svg>
                        <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi">
                        <button class="login-password-toggle" type="button" aria-label="Tampilkan kata sandi" aria-pressed="false" data-password-toggle>
                            <svg class="login-eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="login-eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.2A10 10 0 0 1 12 5c6.4 0 10 7 10 7a16 16 0 0 1-3.1 3.8M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10 10 0 0 0 4.2-.9"/></svg>
                        </button>
                    </div>
                </div>
                <div class="login-form-options">
                    <label><input type="checkbox" name="remember" value="1"><span>Ingat saya</span></label>
                </div>
                <button type="submit" class="login-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M10 17l5-5-5-5m5 5H3m9-9h5a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3h-5"/></svg>
                    Masuk
                </button>
            </form>

            <div class="login-help">
                <span>Belum punya akun?</span>
                <button type="button" data-auth-switch="register">Daftar akun</button>
            </div>
            </div>

            <div class="login-panel login-panel-register {{ $showRegisterMode ? 'is-active' : '' }}" data-auth-panel="register" @unless($showRegisterMode) hidden @endunless>
                <div class="login-form-heading">
                    <span class="login-form-kicker">BUAT AKUN</span>
                    <h2>Daftar Akun</h2>
                    <p>Isi data kampus Anda untuk mulai menggunakan Ruang Kampus.</p>
                </div>
                @if($errors->any())
                    <div class="login-errors" role="alert">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                <form action="{{ route('register') }}" method="POST" class="login-form login-register-form">
                    @csrf
                    <div class="login-field">
                        <label for="register_nama_lengkap">Nama Lengkap</label>
                        <div class="login-input-wrap"><input type="text" id="register_nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required autocomplete="name" placeholder="Nama sesuai identitas kampus"></div>
                    </div>
                    <div class="login-register-row">
                        <div class="login-field">
                            <label for="register_jenis_pengguna">Jenis Pengguna</label>
                            <div class="login-input-wrap"><select id="register_jenis_pengguna" name="jenis_pengguna" required>
                                <option value="" disabled {{ old('jenis_pengguna') ? '' : 'selected' }}>Pilih jenis</option>
                                <option value="mahasiswa" {{ old('jenis_pengguna') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                <option value="dosen" {{ old('jenis_pengguna') === 'dosen' ? 'selected' : '' }}>Dosen</option>
                            </select></div>
                        </div>
                        <div class="login-field">
                            <label for="register_nomor_identitas">Nomor Identitas <span id="register-id-label-hint">NPM / NIDN / NIP</span></label>
                            <div class="login-input-wrap"><input type="text" id="register_nomor_identitas" name="nomor_identitas" value="{{ old('nomor_identitas') }}" required placeholder="NPM / NIDN / NIP"></div>
                        </div>
                    </div>
                    <div class="login-field">
                        <label for="register_email">Email</label>
                        <div class="login-input-wrap"><input type="email" id="register_email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="nama@kampus.ac.id"></div>
                    </div>
                    <div class="login-register-row">
                        <div class="login-field">
                            <label for="register_password">Kata Sandi</label>
                            <div class="login-input-wrap"><input type="password" id="register_password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter"></div>
                        </div>
                        <div class="login-field">
                            <label for="register_password_confirmation">Konfirmasi Sandi</label>
                            <div class="login-input-wrap"><input type="password" id="register_password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi"></div>
                        </div>
                    </div>
                    <button type="submit" class="login-submit">Buat Akun</button>
                </form>
                <div class="login-help"><span>Sudah punya akun?</span><button type="button" data-auth-switch="login">Masuk</button></div>
            </div>
        </div>
    </section>
</div>

<script>
    document.querySelectorAll('[data-password-toggle]').forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            const password = toggle.closest('.login-input-wrap').querySelector('input');
            const shouldShow = password.type === 'password';
            password.type = shouldShow ? 'text' : 'password';
            toggle.setAttribute('aria-pressed', String(shouldShow));
            toggle.setAttribute('aria-label', shouldShow ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            toggle.classList.toggle('is-visible', shouldShow);
        });
    });

    document.querySelectorAll('[data-auth-switch]').forEach(function (switchButton) {
        switchButton.addEventListener('click', function () {
            const target = switchButton.getAttribute('data-auth-switch');
            document.querySelectorAll('[data-auth-panel]').forEach(function (panel) {
                const isTarget = panel.getAttribute('data-auth-panel') === target;
                panel.hidden = !isTarget;
                panel.classList.toggle('is-active', isTarget);
            });
            const firstInput = document.querySelector('[data-auth-panel="' + target + '"] input');
            if (firstInput) firstInput.focus();
        });
    });

    const registerRole = document.getElementById('register_jenis_pengguna');
    const registerId = document.getElementById('register_nomor_identitas');
    const registerIdHint = document.getElementById('register-id-label-hint');
    if (registerRole && registerId && registerIdHint) {
        registerRole.addEventListener('change', function () {
            const isStudent = this.value === 'mahasiswa';
            registerIdHint.textContent = isStudent ? 'NPM Mahasiswa' : 'NIDN / NIP Dosen';
            registerId.placeholder = isStudent ? 'Contoh: 2110511001' : 'Contoh: 0412088501';
        });
    }
</script>
@endsection
