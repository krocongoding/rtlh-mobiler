<x-app-layout>
    <x-slot name="header">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px; width:100%;">
            <div>
                <div class="kicker">
                    <i class="fa-solid fa-clipboard-check"></i> EVALUASI & ASSESSMENT
                </div>
                <h1 style="font-size:28px; font-weight:800; color:var(--slate-900); margin:0;">
                    Assessment RTLH — {{ $house->house_code }}
                </h1>
                <p class="muted" style="margin-top:4px;">
                    Formulir evaluasi tingkat kerusakan dan pembobotan prioritas penerima bantuan.
                </p>
            </div>

            <div class="actions" style="margin:0;">
                <a href="{{ route('houses.show', $house) }}" class="btn">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Detail
                </a>
            </div>
        </div>
    </x-slot>

    <div style="max-width: 960px; margin: 0 auto;">
        {{-- ERROR ALERT --}}
        @if ($errors->any())
            <div class="alert danger" style="border-left:4px solid #b91c1c; margin-bottom:20px;">
                <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
                <div>
                    <strong>Terdapat kesalahan input:</strong>
                    <ul style="margin:6px 0 0 16px; font-size:13px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- SUCCESS ALERT --}}
        @if (session('success'))
            <div class="alert ok" style="margin-bottom:20px;">
                <i class="fa-solid fa-circle-check fa-lg"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('houses.assessment.update', $house) }}">
            @csrf
            @method('PUT')

            {{-- 1. DATA POKOK & SKOR --}}
            <div class="card" style="margin-bottom: 24px;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:18px; border-bottom:1px solid var(--slate-100); padding-bottom:12px;">
                    <div class="stat-widget-icon purple" style="width:34px; height:34px; font-size:15px; margin:0;">
                        1
                    </div>
                    <div>
                        <h3 style="font-size:16px; font-weight:800; color:var(--slate-900); margin:0;">
                            Ringkasan & Skor Assessment
                        </h3>
                        <p class="muted" style="font-size:12px; margin:0;">Informasi waktu survei, status kelayakan, dan penetapan prioritas.</p>
                    </div>
                </div>

                <div class="grid3">
                    <div class="form-group">
                        <label for="assessment_year">Tahun Assessment <span style="color:#e11d48;">*</span></label>
                        <input
                            type="number"
                            id="assessment_year"
                            name="assessment_year"
                            value="{{ old('assessment_year', $assessment?->assessment_year ?? now()->year) }}"
                            min="2000"
                            max="2100"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="assessment_date">Tanggal Survei / Penilaian <span style="color:#e11d48;">*</span></label>
                        <input
                            type="date"
                            id="assessment_date"
                            name="assessment_date"
                            value="{{ old('assessment_date', $assessment?->assessment_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="status">Status Verifikasi <span style="color:#e11d48;">*</span></label>
                        <select id="status" name="status" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="draft" @selected(old('status', $assessment?->status) === 'draft')>Draft</option>
                            <option value="completed" @selected(old('status', $assessment?->status) === 'completed')>Selesai</option>
                            <option value="verified" @selected(old('status', $assessment?->status) === 'verified')>Terverifikasi</option>
                        </select>
                    </div>
                </div>

                <div class="grid2">
                    <div class="form-group">
                        <label for="priority_level">Tingkat Prioritas Bantuan</label>
                        <select id="priority_level" name="priority_level">
                            <option value="">-- Pilih Prioritas --</option>
                            <option value="rendah" @selected(old('priority_level', $assessment?->priority_level) === 'rendah')>Rendah (Kerusakan Ringan)</option>
                            <option value="sedang" @selected(old('priority_level', $assessment?->priority_level) === 'sedang')>Sedang (Kerusakan Sedang)</option>
                            <option value="tinggi" @selected(old('priority_level', $assessment?->priority_level) === 'tinggi')>Tinggi (Kerusakan Berat / Mendesak)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="score">Nilai / Skor Total Penilaian</label>
                        <input
                            type="number"
                            id="score"
                            name="score"
                            value="{{ old('score', $assessment?->score) }}"
                            min="0"
                            step="0.01"
                            placeholder="Contoh: 75.50"
                        >
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label for="notes">Catatan & Rekomendasi Khusus</label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="3"
                        placeholder="Tuliskan catatan umum mengenai urgensi, kondisi sosial ekonomi keluarga, atau pertimbangan teknis..."
                    >{{ old('notes', $assessment?->notes) }}</textarea>
                </div>
            </div>

            {{-- 2. DETAIL PARAMETER PENILAIAN --}}
            @php
                $existingItems = $assessment?->items ?? collect();
                $itemMap = $existingItems->keyBy(function ($item) {
                    return $item->category;
                });
            @endphp

            <div class="card" style="margin-bottom: 24px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--slate-100); padding-bottom:12px;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div class="stat-widget-icon emerald" style="width:34px; height:34px; font-size:15px; margin:0;">
                            2
                        </div>
                        <div>
                            <h3 style="font-size:16px; font-weight:800; color:var(--slate-900); margin:0;">
                                Parameter & Indikator Kelayakan Hunian
                            </h3>
                            <p class="muted" style="font-size:12px; margin:0;">Pilih kondisi untuk masing-masing parameter penilaian fisik & utilitas.</p>
                        </div>
                    </div>
                    <span class="badge published" style="font-size:11px;">
                        {{ $categories->count() }} Kategori Parameter
                    </span>
                </div>

                @if ($categories->count())
                    <div class="grid2" style="gap: 16px;">
                        @foreach ($categories as $category)
                            @php
                                $existing = $itemMap->get($category->code);
                                $selectedValue = old('items.' . $loop->index . '.value', $existing?->value);
                                $existingOtherValue = old('items.' . $loop->index . '.other_value', $existing?->other_value);
                                $existingNotes = old('items.' . $loop->index . '.notes', $existing?->notes);
                                $hasOtherOption = $category->values->contains(function ($val) {
                                    return strtoupper($val->code) === 'OTHER';
                                });
                            @endphp

                            <div style="background:var(--slate-50); border:1px solid var(--slate-200); border-radius:var(--radius-md); padding:16px; display:flex; flex-direction:column; justify-content:space-between;">
                                <div>
                                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                                        <label for="category_{{ $category->id }}" style="font-size:13px; font-weight:700; color:var(--slate-800); margin:0;">
                                            {{ $category->name }}
                                        </label>
                                        <code style="font-size:10px; background:var(--slate-200); padding:1px 5px; border-radius:4px; color:var(--slate-600);">
                                            {{ $category->code }}
                                        </code>
                                    </div>

                                    @if ($category->description)
                                        <p class="muted" style="font-size:11px; margin-bottom:8px; line-height:1.4;">
                                            {{ $category->description }}
                                        </p>
                                    @endif

                                    <input type="hidden" name="items[{{ $loop->index }}][category]" value="{{ $category->code }}">
                                    <input type="hidden" name="items[{{ $loop->index }}][item_code]" value="{{ $category->code }}">

                                    {{-- DROPDOWN NILAI --}}
                                    <select
                                        id="category_{{ $category->id }}"
                                        name="items[{{ $loop->index }}][value]"
                                        onchange="toggleOtherInput({{ $category->id }}, this.value)"
                                        style="background:#fff; margin-bottom:8px;"
                                    >
                                        <option value="">-- Pilih {{ $category->name }} --</option>
                                        @foreach ($category->values as $value)
                                            <option value="{{ $value->code }}" @selected($selectedValue === $value->code)>
                                                {{ $value->label }}
                                            </option>
                                        @endforeach
                                    </select>

                                    {{-- OPSIONAL OTHER --}}
                                    @if ($hasOtherOption)
                                        <div id="other_wrapper_{{ $category->id }}" style="margin-bottom:8px; display:{{ $selectedValue === 'OTHER' ? 'block' : 'none' }};">
                                            <input
                                                type="text"
                                                id="other_{{ $category->id }}"
                                                name="items[{{ $loop->index }}][other_value]"
                                                value="{{ $existingOtherValue }}"
                                                placeholder="Sebutkan keterangan lainnya..."
                                                style="font-size:12px; padding:6px 10px; background:#fff;"
                                            >
                                        </div>
                                    @endif
                                </div>

                                {{-- CATATAN KECIL --}}
                                <div style="margin-top:4px;">
                                    <input
                                        type="text"
                                        id="notes_{{ $category->id }}"
                                        name="items[{{ $loop->index }}][notes]"
                                        value="{{ $existingNotes }}"
                                        placeholder="Catatan tambahan (opsional)..."
                                        style="font-size:12px; padding:6px 10px; background:#fff;"
                                    >
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert info">
                        <i class="fa-solid fa-circle-info fa-lg"></i>
                        <div>Belum ada master kategori assessment yang aktif.</div>
                    </div>
                @endif
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="card" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; padding:18px 24px;">
                <a href="{{ route('houses.show', $house) }}" class="btn">
                    <i class="fa-solid fa-xmark"></i> Batal
                </a>

                <button type="submit" class="btn primary large">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Penilaian Assessment
                </button>
            </div>
        </form>
    </div>

    <script>
        function toggleOtherInput(categoryId, value) {
            const wrapper = document.getElementById('other_wrapper_' + categoryId);
            const input = document.getElementById('other_' + categoryId);

            if (!wrapper) return;

            if (value === 'OTHER') {
                wrapper.style.display = 'block';
            } else {
                wrapper.style.display = 'none';
                if (input) input.value = '';
            }
        }
    </script>
</x-app-layout>
