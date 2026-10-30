@extends('layouts.app')

@section('title', 'Edit Ruangan: ' . $ruangan->nama_ruangan . ' - Biro Pengelola')

@section('content')
<div class="max-w-4xl mx-auto my-6">
    <div class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100">
        <div class="mb-8 border-b border-slate-100 pb-4 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Edit Data Ruangan</h1>
                <p class="text-xs text-slate-500 mt-1">Perbarui informasi ruangan <span class="font-bold text-indigo-600">{{ $ruangan->kode_ruangan }}</span> – {{ $ruangan->nama_ruangan }}</p>
            </div>
            <a href="{{ route('biro.ruangan.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                ← Kembali ke Daftar
            </a>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1">
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
                ✅ {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('biro.ruangan.update', $ruangan) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Kode Ruangan -->
                <div>
                    <label for="kode_ruangan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Kode Ruangan <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="kode_ruangan"
                        name="kode_ruangan"
                        value="{{ old('kode_ruangan', $ruangan->kode_ruangan) }}"
                        required
                        placeholder="Contoh: 303 atau LAB-01"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500 font-mono"
                    >
                </div>

                <!-- Nama Ruangan -->
                <div>
                    <label for="nama_ruangan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nama Ruangan <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="nama_ruangan"
                        name="nama_ruangan"
                        value="{{ old('nama_ruangan', $ruangan->nama_ruangan) }}"
                        required
                        placeholder="Contoh: Ruang Kuliah Multimedia 303"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Jenis Ruangan -->
                <div>
                    <label for="jenis_ruangan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Jenis Ruangan <span class="text-rose-500">*</span>
                    </label>
                    <select id="jenis_ruangan" name="jenis_ruangan" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                        <option value="ruang_kuliah" {{ old('jenis_ruangan', $ruangan->jenis_ruangan) === 'ruang_kuliah' ? 'selected' : '' }}>Ruang Kuliah</option>
                        <option value="laboratorium" {{ old('jenis_ruangan', $ruangan->jenis_ruangan) === 'laboratorium' ? 'selected' : '' }}>Laboratorium</option>
                    </select>
                </div>

                <!-- Lantai -->
                <div>
                    <label for="lantai" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Nomor Lantai <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="lantai" name="lantai" value="{{ old('lantai', $ruangan->lantai) }}" min="1" max="255" step="1" inputmode="numeric" required placeholder="Contoh: 8" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                    <p class="mt-1 text-[11px] text-slate-400">Nomor lantai dapat disesuaikan dengan gedung kampus.</p>
                </div>

                <!-- Status Operasional -->
                <div>
                    <label for="status_operasional" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Status Operasional <span class="text-rose-500">*</span>
                    </label>
                    <select id="status_operasional" name="status_operasional" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                        <option value="aktif" {{ old('status_operasional', $ruangan->status_operasional) === 'aktif' ? 'selected' : '' }}>Aktif & Beroperasi</option>
                        <option value="perawatan" {{ old('status_operasional', $ruangan->status_operasional) === 'perawatan' ? 'selected' : '' }}>Dalam Perawatan</option>
                        <option value="nonaktif" {{ old('status_operasional', $ruangan->status_operasional) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Kapasitas -->
                <div>
                    <label for="kapasitas" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Kapasitas Orang <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="number"
                        id="kapasitas"
                        name="kapasitas"
                        value="{{ old('kapasitas', $ruangan->kapasitas) }}"
                        min="1"
                        required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                    >
                </div>

                <!-- Komputer (Jika Lab) -->
                <div>
                    <label for="jumlah_komputer" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Jumlah Komputer (Khusus Lab)
                    </label>
                    <input
                        type="number"
                        id="jumlah_komputer"
                        name="jumlah_komputer"
                        value="{{ old('jumlah_komputer', $ruangan->jumlah_komputer) }}"
                        min="0"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                    >
                </div>

                <!-- Foto Ruangan -->
                <div>
                    <label for="foto" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Ganti Foto (Opsional, Maks. 2MB)
                    </label>
                    @if($ruangan->foto)
                        <div class="mb-2">
                            <img src="{{ Storage::url($ruangan->foto) }}" alt="Foto {{ $ruangan->nama_ruangan }}" class="h-14 w-full object-cover rounded-lg border border-slate-200">
                            <p class="text-[10px] text-slate-400 mt-1">Foto saat ini — Upload baru untuk mengganti</p>
                        </div>
                    @endif
                    <input
                        type="file"
                        id="foto"
                        name="foto"
                        accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                    >
                </div>
            </div>

            <!-- Keterangan Fasilitas Tambahan -->
            <div>
                <label for="keterangan_tambahan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Keterangan Fasilitas Tambahan</label>
                <textarea
                    id="keterangan_tambahan"
                    name="keterangan_tambahan"
                    rows="3"
                    placeholder="Contoh: Stopkontak tersedia di setiap meja, AC berada di sisi barat ruangan..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"
                >{{ old('keterangan_tambahan', $ruangan->keterangan_tambahan) }}</textarea>
                <p class="mt-1 text-[11px] text-slate-400">Jelaskan letak atau detail fasilitas yang membantu pengguna mengenali kondisi ruangan.</p>
            </div>

            <!-- Checklist Fasilitas Dinamis -->
            @php
                $fasilitasTerpasang = $ruangan->fasilitas->keyBy('id');
            @endphp
            <div class="space-y-3 pt-2 border-t border-slate-100">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Kelola Fasilitas Ruangan ({{ $allFasilitas->count() }})
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @forelse($allFasilitas as $f)
                        @php
                            $isSelected = $fasilitasTerpasang->has($f->id);
                            $pivot = $isSelected ? $fasilitasTerpasang[$f->id]->pivot : null;
                        @endphp
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-xs {{ $isSelected ? 'border-indigo-200 bg-indigo-50/40' : '' }}">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="fasilitas[{{ $f->id }}][selected]"
                                    value="1"
                                    class="w-4 h-4 rounded text-indigo-600"
                                    {{ $isSelected ? 'checked' : '' }}
                                >
                                <span class="font-semibold text-slate-800">{{ $f->nama_fasilitas }}</span>
                                @if($isSelected)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-700 font-bold">Ada</span>
                                @endif
                            </label>
                            <div class="flex items-center gap-1.5">
                                <input
                                    type="number"
                                    name="fasilitas[{{ $f->id }}][jumlah]"
                                    value="{{ $pivot?->jumlah ?? 1 }}"
                                    min="1"
                                    title="Jumlah Unit"
                                    class="w-16 px-2 py-1 text-center bg-white rounded-lg border border-slate-200 text-xs"
                                >
                                <span class="text-[10px] text-slate-400">Unit</span>
                            </div>
                        </div>
                    @empty
                        <p class="sm:col-span-2 rounded-xl border border-dashed border-slate-200 bg-slate-50 p-4 text-xs text-slate-500">Belum ada fasilitas di katalog. Jalankan migration fasilitas untuk memuat daftar awal.</p>
                    @endforelse
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('biro.ruangan.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition">
                    Batal
                </a>
                <button
                    type="submit"
                    class="px-7 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs shadow-md shadow-indigo-200 transition cursor-pointer"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
