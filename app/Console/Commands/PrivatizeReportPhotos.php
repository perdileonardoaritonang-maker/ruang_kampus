<?php

namespace App\Console\Commands;

use App\Models\LaporanKerusakan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PrivatizeReportPhotos extends Command
{
    protected $signature = 'reports:privatize-photos';

    protected $description = 'Move existing report photos from public storage to private storage';

    public function handle(): int
    {
        $public = Storage::disk('public');
        $private = Storage::disk('local');
        $moved = 0;
        $failed = 0;

        LaporanKerusakan::query()
            ->whereNotNull('foto')
            ->chunkById(100, function ($reports) use ($public, $private, &$moved, &$failed) {
                foreach ($reports as $report) {
                    $path = $report->foto;

                    if (!str_starts_with($path, 'laporan_foto/') || str_contains($path, '..')) {
                        $this->warn("Lewati laporan {$report->id}: path foto tidak sesuai.");
                        $failed++;
                        continue;
                    }

                    if (!$public->exists($path)) {
                        if ($private->exists($path)) {
                            continue;
                        }

                        $this->warn("Foto laporan {$report->id} tidak ditemukan di penyimpanan.");
                        $failed++;
                        continue;
                    }

                    $contents = $public->get($path);
                    if (!$private->exists($path)) {
                        $private->put($path, $contents);
                    }

                    if (!$private->exists($path) || $private->get($path) !== $contents) {
                        $this->error("Gagal memverifikasi salinan foto laporan {$report->id}; file publik dipertahankan.");
                        $failed++;
                        continue;
                    }

                    if ($public->delete($path)) {
                        $moved++;
                    } else {
                        $this->warn("Salinan privat berhasil, tetapi file publik laporan {$report->id} belum terhapus.");
                        $failed++;
                    }
                }
            });

        $this->info("Foto dipindahkan: {$moved}. Perlu ditinjau: {$failed}.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
