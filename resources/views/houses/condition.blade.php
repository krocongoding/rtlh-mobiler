<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="kicker">
                <i class="fa-solid fa-house-chimney"></i> KONDISI FISIK & ARSITEKTUR
            </div>
            <h1>Kondisi Fisik Rumah — {{ $house->house_code }}</h1>
            <p class="muted">
                Input dan perbarui data komponen struktural, dinding, lantai, plafon, dan atap rumah.
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

        <form method="POST" action="{{ route('houses.condition.update', $house) }}">
            @csrf
            @method('PUT')

            {{-- 1. STRUKTUR RUMAH --}}
            <div class="card" style="margin-bottom: 20px;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:18px; border-bottom:1px solid var(--slate-100); padding-bottom:12px;">
                    <div class="stat-widget-icon emerald" style="width:34px; height:34px; font-size:15px; margin:0;">
                        1
                    </div>
                    <h3 style="font-size:16px; font-weight:800; color:var(--slate-900); margin:0;">
                        Struktur Utama Rumah
                    </h3>
                </div>

                <div class="grid2">
                    <div class="form-group">
                        <label for="foundation">Pondasi</label>
                        <input
                            type="text"
                            id="foundation"
                            name="foundation"
                            value="{{ old('foundation', $house->structure?->foundation) }}"
                            placeholder="Contoh: Batu kali / Tanpa pondasi"
                        >
                    </div>

                    <div class="form-group">
                        <label for="sloof_condition_id">Kondisi Sloof / Balok Dasar</label>
                        <select id="sloof_condition_id" name="sloof_condition_id">
                            <option value="">-- Pilih Kondisi --</option>
                            @foreach ($conditions as $item)
                                <option value="{{ $item->id }}" @selected(old('sloof_condition_id', $house->structure?->sloof_condition_id) == $item->id)>
                                    {{ $item->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="column_condition_id">Kondisi Kolom / Tiang Penopang</label>
                        <select id="column_condition_id" name="column_condition_id">
                            <option value="">-- Pilih Kondisi --</option>
                            @foreach ($conditions as $item)
                                <option value="{{ $item->id }}" @selected(old('column_condition_id', $house->structure?->column_condition_id) == $item->id)>
                                    {{ $item->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="beam_condition_id">Kondisi Balok Pengikat</label>
                        <select id="beam_condition_id" name="beam_condition_id">
                            <option value="">-- Pilih Kondisi --</option>
                            @foreach ($conditions as $item)
                                <option value="{{ $item->id }}" @selected(old('beam_condition_id', $house->structure?->beam_condition_id) == $item->id)>
                                    {{ $item->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- 2. LANTAI --}}
            <div class="card" style="margin-bottom: 20px;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:18px; border-bottom:1px solid var(--slate-100); padding-bottom:12px;">
                    <div class="stat-widget-icon blue" style="width:34px; height:34px; font-size:15px; margin:0;">
                        2
                    </div>
                    <h3 style="font-size:16px; font-weight:800; color:var(--slate-900); margin:0;">
                        Lantai Rumah
                    </h3>
                </div>

                <div class="grid2">
                    <div class="form-group">
                        <label for="floor_material_id">Bahan Material Lantai</label>
                        <select id="floor_material_id" name="floor_material_id">
                            <option value="">-- Pilih Bahan --</option>
                            @foreach ($floorMaterials as $item)
                                <option value="{{ $item->id }}" @selected(old('floor_material_id', $house->floor?->material_id) == $item->id)>
                                    {{ $item->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="floor_condition_id">Kondisi Lantai</label>
                        <select id="floor_condition_id" name="floor_condition_id">
                            <option value="">-- Pilih Kondisi --</option>
                            @foreach ($conditions as $item)
                                <option value="{{ $item->id }}" @selected(old('floor_condition_id', $house->floor?->condition_id) == $item->id)>
                                    {{ $item->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- 3. DINDING --}}
            <div class="card" style="margin-bottom: 20px;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:18px; border-bottom:1px solid var(--slate-100); padding-bottom:12px;">
                    <div class="stat-widget-icon purple" style="width:34px; height:34px; font-size:15px; margin:0;">
                        3
                    </div>
                    <h3 style="font-size:16px; font-weight:800; color:var(--slate-900); margin:0;">
                        Dinding Rumah
                    </h3>
                </div>

                <div class="grid2">
                    <div class="form-group">
                        <label for="wall_material_id">Bahan Material Dinding</label>
                        <select id="wall_material_id" name="wall_material_id">
                            <option value="">-- Pilih Bahan --</option>
                            @foreach ($wallMaterials as $item)
                                <option value="{{ $item->id }}" @selected(old('wall_material_id', $house->wall?->material_id) == $item->id)>
                                    {{ $item->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="wall_condition_id">Kondisi Dinding</label>
                        <select id="wall_condition_id" name="wall_condition_id">
                            <option value="">-- Pilih Kondisi --</option>
                            @foreach ($conditions as $item)
                                <option value="{{ $item->id }}" @selected(old('wall_condition_id', $house->wall?->condition_id) == $item->id)>
                                    {{ $item->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- 4. PLAFON & ATAP --}}
            <div class="card" style="margin-bottom: 24px;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:18px; border-bottom:1px solid var(--slate-100); padding-bottom:12px;">
                    <div class="stat-widget-icon amber" style="width:34px; height:34px; font-size:15px; margin:0;">
                        4
                    </div>
                    <h3 style="font-size:16px; font-weight:800; color:var(--slate-900); margin:0;">
                        Plafon & Atap Bangunan
                    </h3>
                </div>

                <div class="grid2">
                    <div class="form-group">
                        <label for="ceiling_condition_id">Kondisi Plafon / Langit-langit</label>
                        <select id="ceiling_condition_id" name="ceiling_condition_id">
                            <option value="">-- Pilih Kondisi --</option>
                            @foreach ($conditions as $item)
                                <option value="{{ $item->id }}" @selected(old('ceiling_condition_id', $house->ceiling?->condition_id) == $item->id)>
                                    {{ $item->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="roof_frame_condition_id">Kondisi Rangka Atap</label>
                        <select id="roof_frame_condition_id" name="roof_frame_condition_id">
                            <option value="">-- Pilih Kondisi --</option>
                            @foreach ($conditions as $item)
                                <option value="{{ $item->id }}" @selected(old('roof_frame_condition_id', $house->roof?->frame_condition_id) == $item->id)>
                                    {{ $item->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="roof_material_id">Bahan Penutup Atap</label>
                        <select id="roof_material_id" name="roof_material_id">
                            <option value="">-- Pilih Bahan --</option>
                            @foreach ($roofMaterials as $item)
                                <option value="{{ $item->id }}" @selected(old('roof_material_id', $house->roof?->material_id) == $item->id)>
                                    {{ $item->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="roof_condition_id">Kondisi Penutup Atap</label>
                        <select id="roof_condition_id" name="roof_condition_id">
                            <option value="">-- Pilih Kondisi --</option>
                            @foreach ($conditions as $item)
                                <option value="{{ $item->id }}" @selected(old('roof_condition_id', $house->roof?->condition_id) == $item->id)>
                                    {{ $item->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="actions" style="justify-content: flex-end;">
                <a href="{{ route('houses.show', $house) }}" class="btn">
                    <i class="fa-solid fa-xmark"></i> Batal
                </a>
                <button type="submit" class="btn primary">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Kondisi Fisik
                </button>
            </div>
        </form>
    </div>
</x-app-layout>