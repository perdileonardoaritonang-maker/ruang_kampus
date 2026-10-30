<?php

namespace App\Http\Controllers\Biro;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use App\Models\RiwayatAktivitas;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RuanganController extends Controller
{
    public function index(Request $request)
    {
        $lantai = $request->query('lantai');
        $status = $request->query('status');
        $jenisRuangan = $request->query('jenis_ruangan');
        $search = $request->query('q');

        $query = Ruangan::with('fasilitas');

        if ($lantai) {
            $query->where('lantai', $lantai);
        }

        if ($status) {
            $query->where('status_operasional', $status);
        }

        if ($jenisRuangan) {
            $query->where('jenis_ruangan', $jenisRuangan);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_ruangan', 'like', "%{$search}%")
                  ->orWhere('kode_ruangan', 'like', "%{$search}%");
            });
        }

        $ruanganList = $query->orderBy('lantai')->orderBy('kode_ruangan')->paginate(15)->withQueryString();

        // Ringkasan dibuat hanya untuk lantai yang benar-benar memiliki data ruangan.
        $floorSummary = Ruangan::selectRaw('lantai, count(*) as total, sum(case when jenis_ruangan = "ruang_kuliah" then 1 else 0 end) as total_kuliah, sum(case when jenis_ruangan = "laboratorium" then 1 else 0 end) as total_lab')
            ->groupBy('lantai')
            ->orderBy('lantai')
            ->get()
            ->keyBy('lantai');

        return view('biro.ruangan.index', compact('ruanganList', 'lantai', 'status', 'jenisRuangan', 'search', 'floorSummary'));
    }

    public function create()
    {
        $facilityChoices = ['Air Conditioner (AC)', 'Wi-Fi Kampus', 'Proyektor & Layar Presentasi'];
        $allFasilitas = Fasilitas::whereIn('nama_fasilitas', $facilityChoices)
            ->orderBy('nama_fasilitas')
            ->get();
        return view('biro.ruangan.create', compact('allFasilitas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_ruangan' => ['required', 'string', 'max:20', 'unique:ruangan,kode_ruangan'],
            'nama_ruangan' => ['required', 'string', 'max:100'],
            'jenis_ruangan' => ['required', 'in:ruang_kuliah,laboratorium'],
            'lantai' => ['required', 'integer', 'min:1', 'max:255'],
            'lokasi_keterangan' => ['nullable', 'string', 'max:255'],
            'kapasitas' => ['required', 'integer', 'min:1', 'max:1000'],
            'jumlah_komputer' => ['nullable', 'integer', 'min:0'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'status_operasional' => ['required', 'in:aktif,perawatan,nonaktif'],
            'keterangan_tambahan' => ['nullable', 'string'],
            'fasilitas' => ['nullable', 'array'],
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('ruangan_foto', 'public');
        }

        $ruangan = Ruangan::create([
            'kode_ruangan' => trim($validated['kode_ruangan']),
            'nama_ruangan' => trim($validated['nama_ruangan']),
            'jenis_ruangan' => $validated['jenis_ruangan'],
            'lantai' => $validated['lantai'],
            'lokasi_keterangan' => $validated['lokasi_keterangan'] ?? null,
            'kapasitas' => $validated['kapasitas'],
            'jumlah_komputer' => $validated['jenis_ruangan'] === 'laboratorium' ? ($validated['jumlah_komputer'] ?? 0) : 0,
            'foto' => $fotoPath,
            'status_operasional' => $validated['status_operasional'],
            'keterangan_tambahan' => $validated['keterangan_tambahan'] ?? null,
        ]);

        if (!empty($validated['fasilitas'])) {
            $syncData = [];
            foreach ($validated['fasilitas'] as $fasilitasId => $detail) {
                if (isset($detail['selected'])) {
                    $syncData[$fasilitasId] = [
                        'jumlah' => $detail['jumlah'] ?? 1,
                        'kondisi' => $detail['kondisi'] ?? 'baik',
                    ];
                }
            }
            $ruangan->fasilitas()->sync($syncData);
        }

        RiwayatAktivitas::catat('CREATE_RUANGAN', "Menambahkan ruangan baru: {$ruangan->kode_ruangan} - {$ruangan->nama_ruangan}");

        return redirect()->route('biro.ruangan.index')->with('success', "Ruangan {$ruangan->nama_ruangan} berhasil ditambahkan!");
    }

    public function edit(Ruangan $ruangan)
    {
        $ruangan->load('fasilitas');
        $facilityChoices = ['Air Conditioner (AC)', 'Wi-Fi Kampus', 'Proyektor & Layar Presentasi'];
        $assignedFacilityIds = $ruangan->fasilitas->pluck('id')->all();
        $allFasilitas = Fasilitas::where(function ($query) use ($facilityChoices, $assignedFacilityIds) {
            $query->whereIn('nama_fasilitas', $facilityChoices);
            if ($assignedFacilityIds) {
                $query->orWhereIn('id', $assignedFacilityIds);
            }
        })->orderBy('nama_fasilitas')->get();
        return view('biro.ruangan.edit', compact('ruangan', 'allFasilitas'));
    }

    public function update(Request $request, Ruangan $ruangan)
    {
        $validated = $request->validate([
            'kode_ruangan' => ['required', 'string', 'max:20', 'unique:ruangan,kode_ruangan,' . $ruangan->id],
            'nama_ruangan' => ['required', 'string', 'max:100'],
            'jenis_ruangan' => ['required', 'in:ruang_kuliah,laboratorium'],
            'lantai' => ['required', 'integer', 'min:1', 'max:255'],
            'lokasi_keterangan' => ['nullable', 'string', 'max:255'],
            'kapasitas' => ['required', 'integer', 'min:1', 'max:1000'],
            'jumlah_komputer' => ['nullable', 'integer', 'min:0'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'status_operasional' => ['required', 'in:aktif,perawatan,nonaktif'],
            'keterangan_tambahan' => ['nullable', 'string'],
            'fasilitas' => ['nullable', 'array'],
        ]);

        // Cek nama ruangan bentrok di lantai yang sama
        $existsName = Ruangan::where('lantai', $validated['lantai'])
            ->where('nama_ruangan', trim($validated['nama_ruangan']))
            ->where('id', '!=', $ruangan->id)
            ->exists();

        if ($existsName) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'nama_ruangan' => ["⚠️ PERINGATAN NABRAK: Ruangan dengan nama '" . trim($validated['nama_ruangan']) . "' sudah terdaftar di Lantai {$validated['lantai']}."],
            ]);
        }

        // Cek peminjaman aktif jika status diubah ke perawatan atau nonaktif
        if ($validated['status_operasional'] !== 'aktif') {
            $activeCount = \App\Models\Peminjaman::where('ruangan_id', $ruangan->id)
                ->where('status', 'disetujui')
                ->where(function ($q) {
                    $q->whereDate('tanggal', '>', today())
                      ->orWhere(function ($sub) {
                          $sub->whereDate('tanggal', today())
                              ->where('jam_selesai', '>', now()->format('H:i:s'));
                      });
                })
                ->count();

            if ($activeCount > 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'status_operasional' => [
                        "⚠️ PERINGATAN BENTROK: Ruangan ini masih memiliki {$activeCount} jadwal peminjaman aktif mendatang. Selesaikan atau batalkan peminjaman tersebut terlebih dahulu sebelum mengubah status operasional menjadi '{$validated['status_operasional']}'."
                    ],
                ]);
            }
        }

        if ($request->hasFile('foto')) {
            if ($ruangan->foto && Storage::disk('public')->exists($ruangan->foto)) {
                Storage::disk('public')->delete($ruangan->foto);
            }
            $ruangan->foto = $request->file('foto')->store('ruangan_foto', 'public');
        }

        $ruangan->update([
            'kode_ruangan' => trim($validated['kode_ruangan']),
            'nama_ruangan' => trim($validated['nama_ruangan']),
            'jenis_ruangan' => $validated['jenis_ruangan'],
            'lantai' => $validated['lantai'],
            'lokasi_keterangan' => $validated['lokasi_keterangan'] ?? $ruangan->lokasi_keterangan,
            'kapasitas' => $validated['kapasitas'],
            'jumlah_komputer' => $validated['jenis_ruangan'] === 'laboratorium' ? ($validated['jumlah_komputer'] ?? 0) : 0,
            'status_operasional' => $validated['status_operasional'],
            'keterangan_tambahan' => $validated['keterangan_tambahan'] ?? null,
        ]);

        if (isset($validated['fasilitas'])) {
            $syncData = [];
            foreach ($validated['fasilitas'] as $fasilitasId => $detail) {
                if (isset($detail['selected'])) {
                    $syncData[$fasilitasId] = [
                        'jumlah' => $detail['jumlah'] ?? 1,
                        'kondisi' => $detail['kondisi'] ?? 'baik',
                    ];
                }
            }
            $ruangan->fasilitas()->sync($syncData);
        } else {
            $ruangan->fasilitas()->detach();
        }

        RiwayatAktivitas::catat('UPDATE_RUANGAN', "Memperbarui data ruangan {$ruangan->kode_ruangan}");

        return redirect()->route('biro.ruangan.index')->with('success', "Data ruangan {$ruangan->nama_ruangan} berhasil diperbarui!");
    }

    /**
     * Nonaktifkan ruangan secara aman (bukan hapus permanen agar riwayat peminjaman tetap utuh).
     */
    public function toggleStatus(Ruangan $ruangan)
    {
        $newStatus = $ruangan->status_operasional === 'aktif' ? 'nonaktif' : 'aktif';

        if ($newStatus === 'nonaktif') {
            $activeCount = \App\Models\Peminjaman::where('ruangan_id', $ruangan->id)
                ->where('status', 'disetujui')
                ->where(function ($q) {
                    $q->whereDate('tanggal', '>', today())
                      ->orWhere(function ($sub) {
                          $sub->whereDate('tanggal', today())
                              ->where('jam_selesai', '>', now()->format('H:i:s'));
                      });
                })
                ->count();

            if ($activeCount > 0) {
                return back()->with('error', "⚠️ PERINGATAN BENTROK: Ruangan ini masih memiliki {$activeCount} jadwal peminjaman aktif mendatang. Selesaikan atau batalkan peminjaman terlebih dahulu sebelum menonaktifkan ruangan.");
            }
        }

        $ruangan->status_operasional = $newStatus;
        $ruangan->save();

        RiwayatAktivitas::catat('TOGGLE_RUANGAN', "Mengubah status operasional ruangan {$ruangan->kode_ruangan} menjadi {$newStatus}");

        return back()->with('success', "Status operasional ruangan {$ruangan->nama_ruangan} diubah menjadi {$newStatus}.");
    }
}
