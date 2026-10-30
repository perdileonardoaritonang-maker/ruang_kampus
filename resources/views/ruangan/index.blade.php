@extends('layouts.app')

@section('title', 'Katalog Ruangan & Laboratorium - EduSpace')

@section('content')
<div class="catalog-page">
    <section class="catalog-heading" aria-labelledby="catalog-title">
        <div class="catalog-heading-copy">
            <span class="catalog-eyebrow">Ruang Kampus · Peminjaman Ruangan</span>
            <h1 id="catalog-title">Katalog Ruangan</h1>
            <p>Temukan ruang kampus yang sesuai dengan kebutuhan kegiatan Anda.</p>
        </div>
        <div class="catalog-heading-aside">
            <div class="catalog-total-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M4 21V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v16M2 21h20M9 7h1m4 0h1M9 11h1m4 0h1M9 21v-5h6v5"/></svg>
            </div>
            <div><span>Total ruangan</span><strong>{{ $totalRuangan }} Ruangan</strong></div>
        </div>
        @auth
            @if(auth()->user()->isBiro())
                <a href="{{ route('biro.ruangan.create') }}" class="catalog-add-room">+ Tambah Ruangan</a>
            @endif
        @endauth
    </section>

    <form id="filterForm" action="{{ route('ruangan.index') }}" method="GET" class="catalog-toolbar" aria-label="Filter katalog ruangan">
        <div class="catalog-filter-control">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16M3 21h18M9 7h1m4 0h1M9 11h1m4 0h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5"/></svg>
            <label class="sr-only" for="filter_lantai">Lantai</label>
            <select id="filter_lantai" name="lantai">
                <option value="">Semua lantai</option>
                @foreach($floorOptions as $floor)
                    <option value="{{ $floor }}" {{ (string)$lantai === (string)$floor ? 'selected' : '' }}>Lantai {{ $floor }}</option>
                @endforeach
            </select>
        </div>
        <div class="catalog-filter-control">
            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
            <label class="sr-only" for="filter_hari">Hari</label>
            <select id="filter_hari" name="hari">
                <option value="senin" {{ $hari === 'senin' ? 'selected' : '' }}>Senin</option>
                <option value="selasa" {{ $hari === 'selasa' ? 'selected' : '' }}>Selasa</option>
                <option value="rabu" {{ $hari === 'rabu' ? 'selected' : '' }}>Rabu</option>
                <option value="kamis" {{ $hari === 'kamis' ? 'selected' : '' }}>Kamis</option>
                <option value="jumat" {{ $hari === 'jumat' ? 'selected' : '' }}>Jumat</option>
                <option value="sabtu" {{ $hari === 'sabtu' ? 'selected' : '' }}>Sabtu</option>
            </select>
        </div>
        <div class="catalog-filter-control">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
            <label class="sr-only" for="filter_slot_jam">Waktu</label>
            <select id="filter_slot_jam" name="slot_jam">
                <option value="">Waktu saat ini</option>
                <option value="08:30-11:00" {{ $slotJam === '08:30-11:00' ? 'selected' : '' }}>08.30–11.00 WIB</option>
                <option value="11:00-13:00" {{ $slotJam === '11:00-13:00' ? 'selected' : '' }}>11.00–13.00 WIB</option>
                <option value="13:00-15:30" {{ $slotJam === '13:00-15:30' ? 'selected' : '' }}>13.00–15.30 WIB</option>
                <option value="15:30-18:00" {{ $slotJam === '15:30-18:00' ? 'selected' : '' }}>15.30–18.00 WIB</option>
                <option value="07:00-09:30" {{ $slotJam === '07:00-09:30' ? 'selected' : '' }}>07.00–09.30 WIB</option>
                <option value="18:30-21:00" {{ $slotJam === '18:30-21:00' ? 'selected' : '' }}>18.30–21.00 WIB</option>
            </select>
        </div>
        <div class="catalog-filter-control">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v14H4zM8 9h8m-8 4h5"/></svg>
            <label class="sr-only" for="filter_status">Status</label>
            <select id="filter_status" name="status">
                <option value="" {{ empty($statusFilter) || $statusFilter === 'semua' ? 'selected' : '' }}>Semua status ({{ $totalRuangan }})</option>
                <option value="tersedia" {{ $statusFilter === 'tersedia' ? 'selected' : '' }}>Tersedia ({{ $totalTersedia }})</option>
                <option value="penuh" {{ $statusFilter === 'penuh' ? 'selected' : '' }}>Penuh ({{ $totalPenuh }})</option>
            </select>
        </div>
        <button type="submit" class="catalog-apply-filter">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
            Terapkan Filter
        </button>
        @if($lantai || $slotJam || request()->filled('hari') || $statusFilter)
            <a href="{{ route('ruangan.index') }}" class="catalog-reset-filter">Reset</a>
        @endif
    </form>

    <section class="catalog-results" aria-label="Daftar ruangan">
        <div class="catalog-results-line">
            <p>Menampilkan <strong>{{ $ruanganList->count() }}</strong> dari {{ $totalRuangan }} ruangan</p>
            <span>{{ ucfirst($hari) }} · {{ \Carbon\Carbon::parse($evalDate)->format('d M Y') }} · {{ $hasTimeFilter ? $jamMulai . '–' . $jamSelesai . ' WIB' : 'Cek ' . $jamMulai . '–' . $jamSelesai . ' WIB' }}</span>
        </div>
        <div class="catalog-room-grid">
        @forelse($ruanganList as $ruangan)
            <article class="room-card {{ $ruangan->is_my_booking ? 'is-my-booking' : ($ruangan->status_katalog === 'sudah_penuh' ? 'is-full' : '') }}">
                <div class="room-card-visual">
                    <img src="{{ $ruangan->foto ? \Illuminate\Support\Facades\Storage::url($ruangan->foto) : asset('images/room-placeholder.svg') }}" alt="Foto {{ $ruangan->nama_ruangan }}" loading="lazy">
                    <span class="room-card-code">{{ $ruangan->kode_ruangan }}</span>
                    @if($ruangan->status_katalog === 'tersedia')
                        <span class="room-card-status is-available"><i></i>Tersedia</span>
                    @elseif($ruangan->status_katalog === 'sudah_penuh')
                        <span class="room-card-status is-full"><i></i>Penuh</span>
                    @else
                        <span class="room-card-status is-unavailable"><i></i>{{ $ruangan->status_label }}</span>
                    @endif
                </div>

                <div class="room-card-content">
                    @if($ruangan->is_my_booking)
                        <span class="room-booking-note">✓ Sedang Anda pinjam</span>
                    @endif
                    <div class="room-card-title-row">
                        <h2 title="{{ $ruangan->nama_ruangan }}">{{ $ruangan->nama_ruangan }}</h2>
                        @if(!preg_match('/lt\./i', $ruangan->kode_ruangan))
                            <span class="room-card-floor">Lt. {{ $ruangan->lantai }}</span>
                        @endif
                    </div>
                    <p class="room-card-location">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        <span>{{ $ruangan->lokasi_keterangan ?? 'Gedung Utama Lantai ' . $ruangan->lantai }}</span>
                    </p>

                    <div class="room-card-specs">
                        <span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m7-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7-7.5a4 4 0 0 1 0 7.75M22 21v-2a4 4 0 0 0-3-3.87"/></svg>{{ $ruangan->kapasitas }} kursi</span>
                        <span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h18v13H3zM8 21h8m-4-4v4M7 8h10M7 12h6"/></svg>{{ $ruangan->jenis_ruangan === 'laboratorium' ? $ruangan->jumlah_komputer . ' PC' : 'Ruang kuliah' }}</span>
                    </div>

                    @if($ruangan->status_katalog === 'sudah_penuh' && $ruangan->conflict_reason)
                        <div class="room-card-alert {{ $ruangan->is_my_booking ? 'is-personal' : '' }}" title="{{ $ruangan->conflict_reason }}">
                            {{ $ruangan->is_my_booking ? 'Sudah Anda pinjam' : 'Jadwal bentrok' }} · {{ $ruangan->conflict_reason }}
                        </div>
                    @elseif($ruangan->status_katalog === 'tidak_tersedia' && $ruangan->conflict_reason)
                        <div class="room-card-alert is-maintenance" title="{{ $ruangan->conflict_reason }}">{{ $ruangan->conflict_reason }}</div>
                    @endif

                    <div class="room-card-amenities">
                        @foreach($ruangan->fasilitas->take(3) as $fasilitas)
                            @php
                                $cleanName = str_replace(
                                    ['Air Conditioner (AC)', 'Air Conditioner', 'Proyektor & Layar Presentasi', 'Papan Tulis (Whiteboard)', 'Sound System & Mic Wireless', 'Sambungan Listrik per Meja'],
                                    ['AC', 'AC', 'Proyektor', 'Whiteboard', 'Sound System', 'Stopkontak'],
                                    $fasilitas->nama_fasilitas
                                );
                            @endphp
                            <span>{{ $cleanName }}</span>
                        @endforeach
                        @if($ruangan->fasilitas->count() > 3)
                            <span class="room-amenity-more">+{{ $ruangan->fasilitas->count() - 3 }}</span>
                        @endif
                    </div>
                </div>

                <div class="room-card-actions">
                    <a href="{{ route('ruangan.show', $ruangan) }}" class="room-detail-link">Detail Ruangan <span aria-hidden="true">→</span></a>
                    @auth
                        @if(!auth()->user()->isBiro())
                            @if($ruangan->is_available && $ruangan->status_operasional === 'aktif')
                                <a href="{{ route('peminjaman.create', array_filter(['ruangan_id' => $ruangan->id, 'tanggal' => $evalDate, 'jam_mulai' => $jamMulai, 'jam_selesai' => $jamSelesai])) }}" class="room-book-link">Pinjam</a>
                            @elseif($ruangan->status_katalog === 'sudah_penuh')
                                <span class="room-action-disabled {{ $ruangan->is_my_booking ? 'is-personal' : '' }}" title="{{ $ruangan->conflict_reason }}">{{ $ruangan->is_my_booking ? 'Dipinjam' : 'Penuh' }}</span>
                            @else
                                <span class="room-action-disabled" title="{{ $ruangan->conflict_reason }}">Tutup</span>
                            @endif
                        @endif
                    @else
                        @if($ruangan->is_available && $ruangan->status_operasional === 'aktif')
                            <a href="{{ route('login') }}" class="room-book-link">Pinjam</a>
                        @elseif($ruangan->status_katalog === 'sudah_penuh')
                            <span class="room-action-disabled">Penuh</span>
                        @else
                            <span class="room-action-disabled">Tutup</span>
                        @endif
                    @endauth
                </div>
            </article>
        @empty
            <div class="catalog-empty-state">
                <span aria-hidden="true">⌕</span>
                <h2>Tidak ada ruangan yang cocok</h2>
                <p>Coba ubah pilihan lantai, hari, atau waktu.</p>
                <a href="{{ route('ruangan.index') }}">Reset filter</a>
            </div>
        @endforelse
        </div>
    </section>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('filterForm');
        if (!filterForm) return;

        filterForm.querySelectorAll('select').forEach(function (filter) {
            filter.addEventListener('change', function () {
                filterForm.requestSubmit();
            });
        });
    });
</script>
@endsection
