<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="kicker">
                <i class="fa-solid fa-pen-to-square"></i> EDIT DATA
            </div>
            <h1>Edit Data RTLH {{ $house->house_code }}</h1>
            <p class="muted">
                Perbarui informasi pokok rumah tidak layak huni.
            </p>
        </div>

        <div class="actions">
            <a href="{{ route('houses.show', $house) }}" class="btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Detail
            </a>
        </div>
    </x-slot>

    <div style="max-width: 860px; margin: 0 auto;">
        @if ($errors->any())
            <div class="alert danger" style="border-left:4px solid #b91c1c;">
                <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
                <div>
                    <strong>Terdapat kesalahan pengisian:</strong>
                    <ul style="margin:6px 0 0 16px; font-size:13px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('houses.update', $house) }}">
            @csrf
            @method('PUT')

            {{-- 1. IDENTITAS & WILAYAH --}}
            <div class="card" style="margin-bottom:20px;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:18px; border-bottom:1px solid var(--slate-100); padding-bottom:12px;">
                    <div class="stat-widget-icon emerald" style="width:34px; height:34px; font-size:15px; margin:0;">
                        1
                    </div>
                    <h3 style="font-size:16px; font-weight:800; color:var(--slate-900); margin:0;">
                        Identitas & Wilayah Rumah
                    </h3>
                </div>

                <div class="grid2">
                    <div class="form-group">
                        <label for="house_code">Kode RTLH <span style="color:#e11d48;">*</span></label>
                        <input
                            type="text"
                            id="house_code"
                            name="house_code"
                            value="{{ old('house_code', $house->house_code) }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="survey_year">Tahun Survei <span style="color:#e11d48;">*</span></label>
                        <input
                            type="number"
                            id="survey_year"
                            name="survey_year"
                            value="{{ old('survey_year', $house->survey_year) }}"
                            min="2000"
                            max="2100"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="region_id">Wilayah Administratif (Desa / Kelurahan) <span style="color:#e11d48;">*</span></label>
                    <select id="region_id" name="region_id" required>
                        <option value="">-- Pilih Desa/Kelurahan --</option>
                        @foreach ($villages as $village)
                            <option value="{{ $village->id }}" @selected(old('region_id', $house->region_id) == $village->id)>
                                {{ $village->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- 2. ALAMAT LOKASI --}}
            <div class="card" style="margin-bottom:20px;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:18px; border-bottom:1px solid var(--slate-100); padding-bottom:12px;">
                    <div class="stat-widget-icon blue" style="width:34px; height:34px; font-size:15px; margin:0;">
                        2
                    </div>
                    <h3 style="font-size:16px; font-weight:800; color:var(--slate-900); margin:0;">
                        Alamat & Lokasi
                    </h3>
                </div>

                <div class="form-group">
                    <label for="address">Alamat Lengkap</label>
                    <textarea
                        id="address"
                        name="address"
                        rows="2"
                    >{{ old('address', $house->address) }}</textarea>
                </div>

                <div class="grid3">
                    <div class="form-group">
                        <label for="block">Dusun / Blok</label>
                        <input
                            type="text"
                            id="block"
                            name="block"
                            value="{{ old('block', $house->block) }}"
                        >
                    </div>

                    <div class="form-group">
                        <label for="rt">RT</label>
                        <input
                            type="text"
                            id="rt"
                            name="rt"
                            value="{{ old('rt', $house->rt) }}"
                        >
                    </div>

                    <div class="form-group">
                        <label for="rw">RW</label>
                        <input
                            type="text"
                            id="rw"
                            name="rw"
                            value="{{ old('rw', $house->rw) }}"
                        >
                    </div>
                </div>
            </div>

            {{-- 3. DIMENSI & PENGHUNI --}}
            <div class="card" style="margin-bottom:24px;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:18px; border-bottom:1px solid var(--slate-100); padding-bottom:12px;">
                    <div class="stat-widget-icon purple" style="width:34px; height:34px; font-size:15px; margin:0;">
                        3
                    </div>
                    <h3 style="font-size:16px; font-weight:800; color:var(--slate-900); margin:0;">
                        Dimensi Bangunan & Data Penghuni
                    </h3>
                </div>

                <div class="grid3">
                    <div class="form-group">
                        <label for="area_m2">Luas Rumah (m²)</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            id="area_m2"
                            name="area_m2"
                            value="{{ old('area_m2', $house->area_m2) }}"
                        >
                    </div>

                    <div class="form-group">
                        <label for="occupant_count">Jumlah Penghuni (Jiwa)</label>
                        <input
                            type="number"
                            min="0"
                            id="occupant_count"
                            name="occupant_count"
                            value="{{ old('occupant_count', $house->occupant_count) }}"
                        >
                    </div>

                    <div class="form-group">
                        <label for="household_count">Jumlah KK</label>
                        <input
                            type="number"
                            min="0"
                            id="household_count"
                            name="household_count"
                            value="{{ old('household_count', $house->household_count) }}"
                        >
                    </div>
                </div>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="actions" style="justify-content: flex-end;">
                <a href="{{ route('houses.show', $house) }}" class="btn">
                    <i class="fa-solid fa-xmark"></i> Batal
                </a>
                <button type="submit" class="btn primary">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>