<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\JadwalTetap;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RuanganCatalogController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Tampilkan katalog ruangan dengan filter pencarian & ketersediaan real-time.
     */
    public function index(Request $request)
    {
        // Otomatis sinkronkan peminjaman yang sudah lewat jam selesainya menjadi 'selesai'
        Peminjaman::autoExpirePeminjaman();

        $lantai = $request->query('lantai');
        $floorOptions = Ruangan::query()->select('lantai')->distinct()->orderBy('lantai')->pluck('lantai');
        $slotJam = $request->query('slot_jam');
        $hari = strtolower(trim((string)$request->query('hari')));
        $statusFilter = $request->query('status'); // 'semua', 'tersedia', 'penuh'

        // Handle slot jam (contoh: '08:30-11:00') atau jam_mulai & jam_selesai terpisah
        if ($slotJam && str_contains($slotJam, '-')) {
            [$jamMulai, $jamSelesai] = explode('-', $slotJam);
        } else {
            $jamMulai = $request->query('jam_mulai');
            $jamSelesai = $request->query('jam_selesai');
        }

        // Tampilkan seluruh ruangan agar katalog transparan & lengkap
        $query = Ruangan::with('fasilitas');

        if ($lantai) {
            $query->where('lantai', $lantai);
        }

        $allRuangan = $query->orderBy('lantai')->orderBy('kode_ruangan')->get();

        // Pemetaan hari ke tanggal minggu berjalan untuk evaluasi jadwal bentrok
        $dayIndexMap = [
            'senin' => 0,
            'selasa' => 1,
            'rabu' => 2,
            'kamis' => 3,
            'jumat' => 4,
            'sabtu' => 5,
            'minggu' => 6,
        ];

        // Gunakan tanggal hari ini atau kemunculan berikutnya dari hari yang dipilih,
        // supaya filter hari tidak menghasilkan tanggal lampau yang tidak bisa dipesan.
        if ($hari && isset($dayIndexMap[$hari])) {
            $today = Carbon::today();
            $daysUntilSelected = ($dayIndexMap[$hari] - ($today->dayOfWeekIso - 1) + 7) % 7;
            $evalDate = $today->copy()->addDays($daysUntilSelected)->toDateString();
        } else {
            $evalDate = Carbon::now()->toDateString();
            $hari = BookingService::convertDayToIndonesian(Carbon::now()->dayOfWeek);
        }

        $hasTimeFilter = !empty($jamMulai) && !empty($jamSelesai);
        $evalStart = $hasTimeFilter ? $jamMulai : Carbon::now()->format('H:i');
        $evalEnd = $hasTimeFilter ? $jamSelesai : Carbon::now()->addMinutes(15)->format('H:i');
        $startFmt = strlen($evalStart) === 5 ? $evalStart . ':00' : $evalStart;
        $endFmt = strlen($evalEnd) === 5 ? $evalEnd . ':00' : $evalEnd;

        $currentUserId = auth()->id();

        foreach ($allRuangan as $ruangan) {
            $ruangan->is_my_booking = false;
            $ruangan->my_booking = null;

            if ($ruangan->status_operasional !== 'aktif') {
                $ruangan->status_katalog = 'tidak_tersedia';
                $ruangan->status_label = $ruangan->status_operasional === 'perawatan' ? 'Perawatan' : 'Nonaktif';
                $ruangan->status_color = 'red';
                $ruangan->is_available = false;
                $ruangan->conflict_reason = 'Ruangan sedang ' . $ruangan->status_operasional . ' dan tidak dapat dipinjam.';
                continue;
            }

            // 1. Cek apakah ruangan ini sedang dipinjam oleh user yang login (dan masih aktif belum lewat)
            if ($currentUserId) {
                $myBookingQuery = Peminjaman::where('ruangan_id', $ruangan->id)
                    ->where('user_id', $currentUserId)
                    ->whereDate('tanggal', $evalDate)
                    ->where('status', 'disetujui');

                $myBookingQuery->where('jam_mulai', '<', $endFmt)
                               ->where('jam_selesai', '>', $startFmt);

                $myBooking = $myBookingQuery->first();
                if ($myBooking) {
                    $ruangan->is_my_booking = true;
                    $ruangan->my_booking = $myBooking;
                    $ruangan->status_katalog = 'sudah_penuh';
                    $ruangan->status_label = 'Sudah Penuh';
                    $ruangan->status_color = 'red';
                    $ruangan->is_available = false;
                    $ruangan->conflict_reason = "Anda sedang meminjam ruangan ini (pukul " . substr($myBooking->jam_mulai, 0, 5) . " - " . substr($myBooking->jam_selesai, 0, 5) . " WIB, Kode: {$myBooking->kode_booking}).";
                    continue;
                }
            }

            // Status selalu dihitung berdasarkan rentang waktu yang ditampilkan,
            // baik slot dipilih manual maupun menggunakan waktu berjalan.
            $check = $this->bookingService->checkConflict($ruangan->id, $evalDate, $evalStart, $evalEnd);
            if ($check['conflict']) {
                $ruangan->status_katalog = 'sudah_penuh';
                $ruangan->status_label = 'Sudah Penuh';
                $ruangan->status_color = 'red';
                $ruangan->is_available = false;
                $ruangan->conflict_reason = $check['reason'];
            } else {
                $ruangan->status_katalog = 'tersedia';
                $ruangan->status_label = 'Tersedia';
                $ruangan->status_color = 'green';
                $ruangan->is_available = true;
                $ruangan->conflict_reason = null;
            }
        }

        // Ringkasan Counter Ketersediaan
        $totalRuangan = $allRuangan->count();
        $totalTersedia = $allRuangan->where('is_available', true)->count();
        $totalPenuh = $allRuangan->where('status_katalog', 'sudah_penuh')->count();
        $totalPerawatan = $allRuangan->where('status_katalog', 'tidak_tersedia')->count();

        // Terapkan Quick Filter Status jika dipilih
        if ($statusFilter === 'tersedia') {
            $ruanganList = $allRuangan->where('is_available', true);
        } elseif ($statusFilter === 'penuh') {
            $ruanganList = $allRuangan->where('status_katalog', 'sudah_penuh');
        } else {
            $ruanganList = $allRuangan;
        }

        return view('ruangan.index', compact(
            'ruanganList',
            'totalRuangan',
            'totalTersedia',
            'totalPenuh',
            'totalPerawatan',
            'statusFilter',
            'lantai',
            'floorOptions',
            'slotJam',
            'hari',
            'jamMulai',
            'jamSelesai',
            'hasTimeFilter',
            'evalDate'
        ));
    }

    /**
     * Tampilkan detail ruangan, fasilitas lengkap, dan jadwal kegiatan.
     */
    public function show(Ruangan $ruangan)
    {
        $ruangan->load(['fasilitas', 'jadwalTetap']);

        // Ambil jadwal peminjaman mendatang untuk ruangan ini
        $upcomingBookings = $ruangan->peminjaman()
            ->with('user')
            ->where('status', 'disetujui')
            ->where('tanggal', '>=', today())
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->take(10)
            ->get();

        return view('ruangan.show', compact('ruangan', 'upcomingBookings'));
    }
}
