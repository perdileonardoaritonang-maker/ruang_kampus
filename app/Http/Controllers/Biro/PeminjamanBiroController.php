<?php

namespace App\Http\Controllers\Biro;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanBiroController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Tampilkan seluruh data peminjaman kampus beserta nama & identitas peminjam.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $ruanganId = $request->query('ruangan_id');
        $tanggal = $request->query('tanggal');
        $search = $request->query('q');
        $tab = $request->query('tab', 'aktif');

        if (! in_array($tab, ['aktif', 'kedaluwarsa'], true)) {
            $tab = 'aktif';
        }

        $today = now()->toDateString();
        $currentTime = now()->format('H:i:s');

        $query = Peminjaman::query();

        if ($status) {
            $query->where('status', $status);
        }

        if (! $status || $status === 'disetujui') {
            if ($tab === 'kedaluwarsa') {
                $query->where(function ($q) use ($today, $currentTime) {
                    $q->where('status', 'selesai')
                        ->orWhere(function ($approved) use ($today, $currentTime) {
                            $approved->where('status', 'disetujui')
                                ->where(function ($expired) use ($today, $currentTime) {
                                    $expired->whereDate('tanggal', '<', $today)
                                        ->orWhere(function ($sameDay) use ($today, $currentTime) {
                                            $sameDay->whereDate('tanggal', $today)
                                                ->where('jam_selesai', '<=', $currentTime);
                                        });
                                });
                        });
                });
            } else {
                $query->where('status', 'disetujui')
                    ->where(function ($active) use ($today, $currentTime) {
                        $active->whereDate('tanggal', '>', $today)
                            ->orWhere(function ($sameDay) use ($today, $currentTime) {
                                $sameDay->whereDate('tanggal', $today)
                                    ->where('jam_selesai', '>', $currentTime);
                            });
                    });
            }
        }

        if ($ruanganId) {
            $query->where('ruangan_id', $ruanganId);
        }

        if ($tanggal) {
            $query->where('tanggal', $tanggal);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_booking', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('nama_lengkap', 'like', "%{$search}%")
                            ->orWhere('nomor_identitas', 'like', "%{$search}%");
                    });
            });
        }

        $peminjamanList = $query->with(['user', 'ruangan'])->latest()->paginate(15)->withQueryString();
        $allRuangan = Ruangan::orderBy('lantai')->orderBy('kode_ruangan')->get();
        $activeCount = Peminjaman::where('status', 'disetujui')
            ->where(function ($active) use ($today, $currentTime) {
                $active->whereDate('tanggal', '>', $today)
                    ->orWhere(function ($sameDay) use ($today, $currentTime) {
                        $sameDay->whereDate('tanggal', $today)
                            ->where('jam_selesai', '>', $currentTime);
                    });
            })
            ->count();
        $expiredCount = Peminjaman::where(function ($q) use ($today, $currentTime) {
            $q->where('status', 'selesai')
                ->orWhere(function ($approved) use ($today, $currentTime) {
                    $approved->where('status', 'disetujui')
                        ->where(function ($expired) use ($today, $currentTime) {
                            $expired->whereDate('tanggal', '<', $today)
                                ->orWhere(function ($sameDay) use ($today, $currentTime) {
                                    $sameDay->whereDate('tanggal', $today)
                                        ->where('jam_selesai', '<=', $currentTime);
                                });
                        });
                });
        })->count();

        return view('biro.peminjaman.index', compact(
            'peminjamanList',
            'allRuangan',
            'status',
            'ruanganId',
            'tanggal',
            'search',
            'tab',
            'activeCount',
            'expiredCount'
        ));
    }

    /**
     * Pembatalan sepihak oleh Biro (Wajib menyertakan alasan resmi).
     */
    public function cancel(Request $request, Peminjaman $peminjaman)
    {
        $validated = $request->validate([
            'alasan_pembatalan' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'alasan_pembatalan.required' => 'Alasan pembatalan oleh Biro wajib diisi.',
            'alasan_pembatalan.min' => 'Alasan pembatalan minimal 5 karakter.',
        ]);

        $this->bookingService->cancelByBiro($peminjaman, Auth::user(), $validated['alasan_pembatalan']);

        return back()->with('success', "Peminjaman {$peminjaman->kode_booking} berhasil dibatalkan resmi oleh Biro.");
    }
}
