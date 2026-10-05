<x-app-layout>
    <x-slot name="header">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px; width:100%;">
            <div>
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                    @if($house->status === 'published')
                        <span class="badge published"><i class="fa-solid fa-circle-check"></i> Published</span>
                    @elseif($house->status === 'verified')
                        <span class="badge" style="background:#ede9fe; color:#6d28d9;"><i class="fa-solid fa-check-double"></i> Verified</span>
                    @elseif($house->status === 'submitted')
                        <span class="badge submitted"><i class="fa-solid fa-hourglass-half"></i> Submitted</span>
                    @elseif($house->status === 'revision')
                        <span class="badge revision"><i class="fa-solid fa-rotate-left"></i> Perlu Revisi</span>
                    @else
                        <span class="badge draft">{{ ucfirst($house->status) }}</span>
                    @endif

                    @php
                        $latestAssessment = $house->assessments
                            ->sortByDesc('assessment_year')
                            ->sortByDesc('id')
                            ->first() ?? $house->latestAssessment;
                    @endphp

                    @if($latestAssessment?->priority_level)
                        @php
                            $pri = strtolower($latestAssessment->priority_level);
                            $badgeClass = ($pri === 'tinggi' || $pri === 'berat') ? 'badge priority-high' : (($pri === 'sedang') ? 'badge priority-medium' : 'badge priority-low');
                        @endphp
                        <span class="{{ $badgeClass }}">
                            Prioritas {{ ucfirst($latestAssessment->priority_level) }}
                        </span>
                    @endif
                </div>

                <h1 style="font-size:28px; font-weight:800; color:var(--slate-900); margin:0;">
                    <i class="fa-solid fa-house text-emerald-600" style="color:#059669;"></i>
                    {{ $house->house_code }}
                </h1>

                <p class="muted" style="margin-top:4px; font-size:14px;">
                    <i class="fa-solid fa-location-dot"></i> {{ $house->region?->name ?? 'Kabupaten Cirebon' }} &bull; Tahun Survei {{ $house->survey_year }}
                </p>
            </div>

            {{-- ACTION HUB --}}
            <div class="actions" style="margin:0;">
                @can('update', $house)
                    <a href="{{ route('houses.edit', $house) }}" class="btn small" title="Edit Data Pokok">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Pokok
                    </a>
                    <a href="{{ route('houses.condition.edit', $house) }}" class="btn small" title="Edit Kondisi Fisik">
                        <i class="fa-solid fa-house-chimney"></i> Kondisi Fisik
                    </a>
                    <a href="{{ route('houses.sanitation-utility.edit', $house) }}" class="btn small" title="Edit Sanitasi">
                        <i class="fa-solid fa-faucet-detergent"></i> Sanitasi
                    </a>
                    <a href="{{ route('houses.occupants.edit', $house) }}" class="btn small" title="Kelola Penghuni">
                        <i class="fa-solid fa-users"></i> Penghuni ({{ $house->occupants->count() }})
                    </a>
                    <a href="{{ route('houses.assessment.edit', $house) }}" class="btn small" title="Edit Assessment">
                        <i class="fa-solid fa-clipboard-check"></i> Assessment
                    </a>
                @endcan

                @can('delete', $house)
                    <form method="POST" action="{{ route('houses.destroy', $house) }}" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data RTLH ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn small danger">
                            <i class="fa-solid fa-trash-can"></i> Hapus
                        </button>
                    </form>
                @endcan

                <a href="{{ (isset($panel) && $panel === 'surveyor') ? route('surveyor.rtlh.index') : route('houses.index') }}" class="btn small">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </x-slot>

    @push('head')
        @if($house->latitude && $house->longitude)
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
        @endif
    @endpush

    {{-- FLASH SUCCESS MESSAGE --}}
    @if (session('success') || session('ok'))
        <div class="alert ok" style="margin-bottom: 24px;">
            <i class="fa-solid fa-circle-check fa-lg"></i>
            <div>{{ session('success') ?? session('ok') }}</div>
        </div>
    @endif

    {{-- QUICK SPECS KPI --}}
    <div class="grid4" style="margin-bottom: 24px;">
        <div class="stat-widget">
            <div class="stat-label">Luas Bangunan</div>
            <div class="stat" style="font-size: 24px;">
                {{ $house->area_m2 ? number_format((float) $house->area_m2, 1, ',', '.') : '-' }} <span style="font-size:15px; font-weight:600; color:var(--slate-500);">m²</span>
            </div>
            <div class="stat-desc">Luas lantai hunian</div>
        </div>

        <div class="stat-widget">
            <div class="stat-label">Penghuni & KK</div>
            <div class="stat" style="font-size: 24px;">
                {{ $house->occupant_count ?? $house->occupants->count() }} <span style="font-size:15px; font-weight:600; color:var(--slate-500);">Jiwa</span>
            </div>
            <div class="stat-desc">{{ $house->household_count ?: 1 }} Kepala Keluarga</div>
        </div>

        <div class="stat-widget">
            <div class="stat-label">Skor Penilaian</div>
            <div class="stat" style="font-size: 24px; color: var(--primary-700);">
                {{ $latestAssessment?->score ?? 'N/A' }}
            </div>
            <div class="stat-desc">Prioritas: {{ ucfirst($latestAssessment?->priority_level ?? '-') }}</div>
        </div>

        <div class="stat-widget">
            <div class="stat-label">Status Alur Data</div>
            <div class="stat" style="font-size: 20px; color: #059669;">
                {{ ucfirst($house->status) }}
            </div>
            <div class="stat-desc">{{ $house->is_public ? 'Publik (Peta Aktif)' : 'Data Internal / Draft' }}</div>
        </div>
    </div>

    {{-- GRID 2 COLUMNS: IDENTITAS & MINI-MAP --}}
    <div class="grid2" style="margin-bottom: 24px;">
        {{-- INFORMASI POKOK --}}
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--slate-100); padding-bottom:12px;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <div class="stat-widget-icon blue" style="width:34px; height:34px; font-size:15px; margin:0;">
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <h3 style="font-size:16px; font-weight:800; color:var(--slate-900); margin:0;">
                        Identitas & Wilayah Rumah
                    </h3>
                </div>
                @can('update', $house)
                    <a href="{{ route('houses.edit', $house) }}" class="btn small" style="padding:4px 8px; font-size:11px;">
                        <i class="fa-solid fa-pen"></i> Edit
                    </a>
                @endcan
            </div>

            <table style="font-size:13px;">
                <tbody>
                    <tr>
                        <td style="width:150px; color:var(--slate-500); font-weight:600;">Kode RTLH</td>
                        <td><strong style="color:var(--slate-900); font-family:monospace;">{{ $house->house_code }}</strong></td>
                    </tr>
                    <tr>
                        <td style="color:var(--slate-500); font-weight:600;">Wilayah</td>
                        <td><span class="badge" style="background:var(--slate-100);">{{ $house->region?->name ?? '-' }}</span></td>
                    </tr>
                    <tr>
                        <td style="color:var(--slate-500); font-weight:600;">Alamat Lengkap</td>
                        <td>{{ $house->address ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color:var(--slate-500); font-weight:600;">Dusun / Blok</td>
                        <td>{{ $house->block ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color:var(--slate-500); font-weight:600;">RT / RW</td>
                        <td>RT {{ $house->rt ?: '-' }} / RW {{ $house->rw ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color:var(--slate-500); font-weight:600;">Tahun Survei</td>
                        <td>{{ $house->survey_year }}</td>
                    </tr>
                    <tr>
                        <td style="color:var(--slate-500); font-weight:600;">Kawasan / Kepemilikan</td>
                        <td>{{ $house->settlementCondition?->label ?? '-' }} &bull; {{ $house->ownershipStatus?->label ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- GEOSPASIAL & MINI MAP --}}
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--slate-100); padding-bottom:12px;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <div class="stat-widget-icon emerald" style="width:34px; height:34px; font-size:15px; margin:0;">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <h3 style="font-size:16px; font-weight:800; color:var(--slate-900); margin:0;">
                        Lokasi Geospasial
                    </h3>
                </div>
            </div>

            @if($house->latitude !== null && $house->longitude !== null)
                <div id="mini-map" style="height:210px; width:100%; border-radius:var(--radius-md); border:1px solid var(--slate-200); margin-bottom:14px;"></div>

                <div style="display:flex; justify-content:space-between; gap:12px; background:var(--slate-50); padding:10px 14px; border-radius:var(--radius-md); font-size:12px;">
                    <div>
                        <span class="muted">Lat:</span>
                        <code style="font-weight:700; color:var(--slate-800);">{{ $house->latitude }}</code>
                    </div>
                    <div>
                        <span class="muted">Lng:</span>
                        <code style="font-weight:700; color:var(--slate-800);">{{ $house->longitude }}</code>
                    </div>
                </div>
            @else
                <div style="padding:48px 24px; text-align:center; background:var(--slate-50); border-radius:var(--radius-md);" class="muted">
                    <i class="fa-solid fa-location-pin-slash fa-2x" style="color:var(--slate-400); margin-bottom:10px;"></i>
                    <p style="font-size:13px; margin:0;">Koordinat GPS belum diinput. Anda dapat memperbaruinya melalui form kondisi.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- KONDISI FISIK, SANITASI, UTILITAS --}}
    <div class="grid3" style="margin-bottom: 24px;">
        {{-- STRUKTUR --}}
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; border-bottom:1px solid var(--slate-100); padding-bottom:8px;">
                <h4 style="font-size:15px; font-weight:800; color:var(--slate-900); margin:0;">
                    <i class="fa-solid fa-cubes text-emerald-600" style="color:#059669;"></i> Struktur Utama
                </h4>
                @can('update', $house)
                    <a href="{{ route('houses.condition.edit', $house) }}" class="btn small" style="padding:2px 8px; font-size:11px;">Edit</a>
                @endcan
            </div>

            <div style="display:flex; flex-direction:column; gap:8px; font-size:13px;">
                <div style="display:flex; justify-content:space-between; border-bottom:1px dashed var(--slate-100); padding-bottom:4px;">
                    <span class="muted">Pondasi:</span>
                    <strong>{{ $house->structure?->foundation ?: '-' }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; border-bottom:1px dashed var(--slate-100); padding-bottom:4px;">
                    <span class="muted">Sloof:</span>
                    <strong>{{ $house->structure?->sloofCondition?->label ?: '-' }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; border-bottom:1px dashed var(--slate-100); padding-bottom:4px;">
                    <span class="muted">Kolom / Tiang:</span>
                    <strong>{{ $house->structure?->columnCondition?->label ?: '-' }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span class="muted">Balok:</span>
                    <strong>{{ $house->structure?->beamCondition?->label ?: '-' }}</strong>
                </div>
            </div>
        </div>

        {{-- KOMPONEN RUMAH --}}
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; border-bottom:1px solid var(--slate-100); padding-bottom:8px;">
                <h4 style="font-size:15px; font-weight:800; color:var(--slate-900); margin:0;">
                    <i class="fa-solid fa-house-chimney text-sky-600" style="color:#0284c7;"></i> Komponen Fisik
                </h4>
                @can('update', $house)
                    <a href="{{ route('houses.condition.edit', $house) }}" class="btn small" style="padding:2px 8px; font-size:11px;">Edit</a>
                @endcan
            </div>

            <div style="display:flex; flex-direction:column; gap:8px; font-size:13px;">
                <div style="display:flex; justify-content:space-between; border-bottom:1px dashed var(--slate-100); padding-bottom:4px;">
                    <span class="muted">Lantai:</span>
                    <span>{{ $house->floor?->material?->label ?: '-' }} ({{ $house->floor?->condition?->label ?: '-' }})</span>
                </div>
                <div style="display:flex; justify-content:space-between; border-bottom:1px dashed var(--slate-100); padding-bottom:4px;">
                    <span class="muted">Dinding:</span>
                    <span>{{ $house->wall?->material?->label ?: '-' }} ({{ $house->wall?->condition?->label ?: '-' }})</span>
                </div>
                <div style="display:flex; justify-content:space-between; border-bottom:1px dashed var(--slate-100); padding-bottom:4px;">
                    <span class="muted">Atap:</span>
                    <span>{{ $house->roof?->material?->label ?: '-' }} ({{ $house->roof?->condition?->label ?: '-' }})</span>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span class="muted">Plafon:</span>
                    <span>{{ $house->ceiling?->condition?->label ?: '-' }}</span>
                </div>
            </div>
        </div>

        {{-- SANITASI & UTILITAS --}}
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; border-bottom:1px solid var(--slate-100); padding-bottom:8px;">
                <h4 style="font-size:15px; font-weight:800; color:var(--slate-900); margin:0;">
                    <i class="fa-solid fa-faucet-detergent text-purple-600" style="color:#7c3aed;"></i> Sanitasi & Utilitas
                </h4>
                @can('update', $house)
                    <a href="{{ route('houses.sanitation-utility.edit', $house) }}" class="btn small" style="padding:2px 8px; font-size:11px;">Edit</a>
                @endcan
            </div>

            <div style="display:flex; flex-direction:column; gap:8px; font-size:13px;">
                <div style="display:flex; justify-content:space-between; border-bottom:1px dashed var(--slate-100); padding-bottom:4px;">
                    <span class="muted">Sumber Air:</span>
                    <strong>{{ $house->sanitation?->waterSource?->label ?: '-' }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; border-bottom:1px dashed var(--slate-100); padding-bottom:4px;">
                    <span class="muted">Jamban:</span>
                    <strong>{{ $house->sanitation?->toilet_available ? 'Ada' : 'Tidak Punya' }} ({{ $house->sanitation?->toiletType?->label ?: '-' }})</strong>
                </div>
                <div style="display:flex; justify-content:space-between; border-bottom:1px dashed var(--slate-100); padding-bottom:4px;">
                    <span class="muted">TPA Tinja:</span>
                    <strong>{{ $house->sanitation?->fecalDisposalType?->label ?: '-' }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span class="muted">Penerangan:</span>
                    <strong>{{ $house->utility?->lightingSource?->label ?: '-' }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- FOTO DOKUMENTASI FISIK --}}
    @if($house->photos && $house->photos->isNotEmpty())
        <div class="card" style="margin-bottom: 24px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--slate-100); padding-bottom:12px;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <div class="stat-widget-icon rose" style="width:34px; height:34px; font-size:15px; margin:0;">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                    <h3 style="font-size:16px; font-weight:800; color:var(--slate-900); margin:0;">
                        Dokumentasi Foto Lapangan
                    </h3>
                </div>
                <span class="badge" style="background:var(--slate-100);">{{ $house->photos->count() }} Foto Tersimpan</span>
            </div>

            <div class="photos" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); gap:16px;">
                @foreach($house->photos as $photo)
                    <div style="border:1px solid var(--slate-200); border-radius:var(--radius-md); overflow:hidden; background:var(--slate-50);">
                        <button type="button" onclick="openPhotoPreview(@js(asset('storage/' . $photo->path)), @js($photo->caption ?? ucfirst(str_replace(['photo_', '_'], ['', ' '], $photo->type ?? 'Foto'))))" style="display:block; width:100%; border:0; padding:0; background:none; cursor:zoom-in;" title="Klik untuk melihat foto lebih besar">
                            <img
                                src="{{ asset('storage/' . $photo->path) }}"
                                alt="{{ $photo->caption ?? 'Foto Rumah' }}"
                                style="width:100%; height:160px; object-fit:cover; display:block;"
                                onerror="this.style.display='none'; this.parentElement.nextElementSibling.style.display='flex';"
                            >
                        </button>
                        <div style="display:none; height:160px; align-items:center; justify-content:center; background:var(--slate-100); color:var(--slate-400); font-size:13px;">
                            <i class="fa-solid fa-image-slash"></i> Foto tidak ditemukan
                        </div>
                        <div style="padding:8px 12px; font-size:12px; font-weight:700; color:var(--slate-700);">
                            {{ ucfirst(str_replace(['photo_', '_'], ['', ' '], $photo->type ?? $photo->caption ?? 'Foto')) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div id="photo-preview-modal" role="dialog" aria-modal="true" aria-label="Pratinjau foto dokumentasi" onclick="closePhotoPreview()" style="display:none; position:fixed; inset:0; z-index:2000; padding:24px; background:rgba(2, 6, 23, .88); align-items:center; justify-content:center;">
        <div onclick="event.stopPropagation()" style="position:relative; max-width:min(1100px, 100%); max-height:100%;">
            <button type="button" onclick="closePhotoPreview()" aria-label="Tutup pratinjau foto" style="position:absolute; top:12px; right:12px; z-index:1; width:38px; height:38px; border:0; border-radius:50%; background:rgba(15,23,42,.8); color:#fff; font-size:20px; cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
            <img id="photo-preview-image" src="" alt="" style="display:block; max-width:100%; max-height:82vh; border-radius:var(--radius-md); object-fit:contain;">
            <p id="photo-preview-caption" style="margin:10px 0 0; text-align:center; color:#fff; font-size:14px;"></p>
        </div>
    </div>

    {{-- DATA PENGHUNI --}}
    @if($house->occupants && $house->occupants->isNotEmpty())
        <div class="card" style="margin-bottom: 24px; padding:0; overflow:hidden;">
            <div style="padding: 18px 24px; border-bottom: 1px solid var(--slate-200); background: var(--slate-50); display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <i class="fa-solid fa-users text-blue-600" style="color:#0284c7;"></i>
                    <h3 style="font-size:16px; font-weight:800; color:var(--slate-900); margin:0;">
                        Daftar Anggota Penghuni Rumah
                    </h3>
                </div>
                @can('update', $house)
                    <a href="{{ route('houses.occupants.edit', $house) }}" class="btn small">
                        <i class="fa-solid fa-user-plus"></i> Kelola Penghuni
                    </a>
                @endcan
            </div>

            <div class="tablewrap" style="border:none; border-radius:0;">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>NIK</th>
                            <th>Hubungan</th>
                            <th>Jenis Kelamin</th>
                            <th>Usia</th>
                            <th>Pendidikan / Pekerjaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($house->occupants as $idx => $occ)
                            <tr>
                                <td class="muted">{{ $idx + 1 }}</td>
                                <td><strong>{{ $occ->name ?: '-' }}</strong></td>
                                <td><code style="background:var(--slate-100); padding:2px 6px; border-radius:4px;">{{ $occ->nik ?: '-' }}</code></td>
                                <td><span class="badge" style="background:var(--slate-100);">{{ $occ->relationship ?: '-' }}</span></td>
                                <td>{{ $occ->gender === 'M' || $occ->gender === 'L' ? 'Laki-laki' : ($occ->gender === 'F' || $occ->gender === 'P' ? 'Perempuan' : '-') }}</td>
                                <td>{{ $occ->age ? $occ->age . ' Th' : '-' }}</td>
                                <td>{{ $occ->education ?: '-' }} / {{ $occ->job ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- HASIL ASSESSMENT --}}
    @if($latestAssessment)
        <div class="card" style="margin-bottom: 24px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--slate-100); padding-bottom:12px;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <div class="stat-widget-icon purple" style="width:34px; height:34px; font-size:15px; margin:0;">
                        <i class="fa-solid fa-clipboard-list"></i>
                    </div>
                    <h3 style="font-size:16px; font-weight:800; color:var(--slate-900); margin:0;">
                        Hasil Assessment & Skor Penilaian
                    </h3>
                </div>
                @can('update', $house)
                    <a href="{{ route('houses.assessment.edit', $house) }}" class="btn small primary">
                        <i class="fa-solid fa-pen"></i> Edit Assessment
                    </a>
                @endcan
            </div>

            <div class="grid4" style="margin-bottom:16px;">
                <div style="background:var(--slate-50); padding:12px 16px; border-radius:var(--radius-md);">
                    <div class="muted" style="font-size:11px;">TAHUN ASSESSMENT</div>
                    <div style="font-size:18px; font-weight:800; color:var(--slate-900);">{{ $latestAssessment->assessment_year }}</div>
                </div>
                <div style="background:var(--slate-50); padding:12px 16px; border-radius:var(--radius-md);">
                    <div class="muted" style="font-size:11px;">TANGGAL SURVEI</div>
                    <div style="font-size:18px; font-weight:800; color:var(--slate-900);">{{ $latestAssessment->assessment_date?->format('d/m/Y') ?? '-' }}</div>
                </div>
                <div style="background:var(--slate-50); padding:12px 16px; border-radius:var(--radius-md);">
                    <div class="muted" style="font-size:11px;">NILAI / SCORE TOTAL</div>
                    <div style="font-size:22px; font-weight:800; color:var(--primary-700);">{{ $latestAssessment->score ?? '-' }}</div>
                </div>
                <div style="background:var(--slate-50); padding:12px 16px; border-radius:var(--radius-md);">
                    <div class="muted" style="font-size:11px;">TINGKAT PRIORITAS</div>
                    <div style="font-size:18px; font-weight:800; color:var(--slate-900);">{{ ucfirst($latestAssessment->priority_level ?? '-') }}</div>
                </div>
            </div>

            @if($latestAssessment->notes)
                <div style="background:var(--slate-50); padding:14px 18px; border-radius:var(--radius-md); border-left:4px solid var(--primary-500); margin-bottom:16px;">
                    <div style="font-size:12px; font-weight:700; color:var(--slate-700); margin-bottom:2px;">Catatan Penilai:</div>
                    <p style="font-size:13px; color:var(--slate-700); margin:0;">{{ $latestAssessment->notes }}</p>
                </div>
            @endif

            @if($latestAssessment->items && $latestAssessment->items->isNotEmpty())
                <div class="tablewrap" style="margin-top:14px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Kategori Parameter</th>
                                <th>Nilai / Kondisi</th>
                                <th>Catatan Tambahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($latestAssessment->items as $item)
                                <tr>
                                    <td><strong>{{ ucwords(str_replace('_', ' ', $item->category)) }}</strong></td>
                                    <td>
                                        <span class="badge" style="background:var(--slate-100); color:var(--slate-800);">
                                            {{ $item->value ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="muted">{{ $item->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    @push('scripts')
        <script>
            function openPhotoPreview(source, caption) {
                document.getElementById('photo-preview-image').src = source;
                document.getElementById('photo-preview-image').alt = caption;
                document.getElementById('photo-preview-caption').textContent = caption;
                document.getElementById('photo-preview-modal').style.display = 'flex';
            }

            function closePhotoPreview() {
                document.getElementById('photo-preview-modal').style.display = 'none';
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') closePhotoPreview();
            });
        </script>
        @if($house->latitude !== null && $house->longitude !== null)
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const lat = {{ $house->latitude }};
                    const lng = {{ $house->longitude }};
                    const miniMap = L.map('mini-map', { zoomControl: false, scrollWheelZoom: false }).setView([lat, lng], 15);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(miniMap);

                    L.marker([lat, lng]).addTo(miniMap)
                        .bindPopup('<strong>{{ $house->house_code }}</strong><br>{{ $house->region?->name ?? "Cirebon" }}')
                        .openPopup();
                });
            </script>
        @endif
    @endpush
</x-app-layout>
