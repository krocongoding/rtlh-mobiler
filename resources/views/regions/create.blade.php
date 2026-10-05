<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="kicker">
                <i class="fa-solid fa-map-pin"></i> DATA MASTER
            </div>
            <h1>Tambah Wilayah Baru</h1>
            <p class="muted">
                Daftarkan kecamatan atau desa/kelurahan baru ke dalam sistem.
            </p>
        </div>

        <div class="actions">
            <a href="{{ route('regions.index') }}" class="btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div style="max-width: 680px; margin: 0 auto;">
        <div class="card">
            <form method="POST" action="{{ route('regions.store') }}">
                @csrf

                <div class="form-group">
                    <label for="parent_id">Wilayah Induk (Kecamatan)</label>
                    <select id="parent_id" name="parent_id">
                        <option value="">-- Tidak Ada (Tingkat Kecamatan) --</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" @selected(old('parent_id') == $parent->id)>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <p style="color:#b91c1c; font-size:12px; margin-top:4px;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="code">Kode Wilayah Kemendagri / BPS</label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" placeholder="Contoh: 32.09.01">
                    @error('code')
                        <p style="color:#b91c1c; font-size:12px; margin-top:4px;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name">Nama Wilayah</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Kecamatan Sumber">
                    @error('name')
                        <p style="color:#b91c1c; font-size:12px; margin-top:4px;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid2">
                    <div class="form-group">
                        <label for="type">Tipe Wilayah</label>
                        <select id="type" name="type">
                            <option value="Kecamatan" @selected(old('type') === 'Kecamatan')>Kecamatan</option>
                            <option value="desa" @selected(old('type') === 'desa')>Desa / Kelurahan</option>
                            <option value="Kabupaten" @selected(old('type') === 'Kabupaten')>Kabupaten</option>
                        </select>
                        @error('type')
                            <p style="color:#b91c1c; font-size:12px; margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="level">Level Hirarki</label>
                        <input type="number" id="level" name="level" value="{{ old('level', 1) }}" min="0">
                        @error('level')
                            <p style="color:#b91c1c; font-size:12px; margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div style="display:flex; align-items:center; gap:8px; margin-top:10px; margin-bottom:24px;">
                    <input type="checkbox" id="is_active" name="is_active" value="1" checked style="width:auto; margin:0;">
                    <label for="is_active" style="margin:0; cursor:pointer;">Wilayah aktif</label>
                </div>

                <div class="actions">
                    <button type="submit" class="btn primary">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Wilayah
                    </button>
                    <a href="{{ route('regions.index') }}" class="btn">
                        <i class="fa-solid fa-xmark"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>