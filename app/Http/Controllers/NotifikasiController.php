<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Tampilkan seluruh notifikasi milik pengguna yang sedang login.
     */
    public function index()
    {
        $user = Auth::user();

        $notifikasiList = Notifikasi::untukUser($user)
            ->latest()
            ->paginate(15);

        return view('notifikasi.index', compact('notifikasiList'));
    }

    /**
     * Tandai notifikasi sebagai dibaca dan redirect ke target URL jika ada.
     */
    public function read(Notifikasi $notifikasi)
    {
        $user = Auth::user();

        // Validasi hak akses notifikasi
        if (!$user->isBiro() && $notifikasi->user_id !== $user->id) {
            abort(403);
        }

        $notifikasi->tandaiDibaca();

        if ($notifikasi->url) {
            return redirect($notifikasi->url);
        }

        return back()->with('success', 'Notifikasi telah ditandai dibaca.');
    }

    /**
     * Tandai semua notifikasi pengguna sebagai telah dibaca.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();

        Notifikasi::untukUser($user)
            ->belumDibaca()
            ->update([
                'dibaca' => true,
                'dibaca_pada' => now(),
            ]);

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }
}
