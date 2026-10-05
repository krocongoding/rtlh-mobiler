<x-layouts.app title="Admin Command Center">

    <div class="pagehead">
        <div>
            <div class="kicker">
                <i class="fa-solid fa-crown"></i> ADMINISTRATOR WORKSPACE
            </div>
            <h1>Command Center RTLH</h1>
            <p class="muted">
                Monitoring status integrasi data survei lapangan, proses review verifikasi, dan kontrol publikasi portal.
            </p>
        </div>

        <div class="actions">
            <a class="btn primary" href="{{ route('admin.review.index') }}">
                <i class="fa-solid fa-clipboard-check"></i> Antrian Review
                @if($submitted > 0)
                    <span class="badge" style="background:#fef3c7; color:#b45309; padding:2px 6px; font-size:11px;">
                        {{ $submitted }}
                    </span>
                @endif
            </a>
            <a class="btn" href="{{ route('houses.create') }}">
                <i class="fa-solid fa-plus"></i> Tambah Data
            </a>
        </div>
    </div>

    {{-- METRIC CARDS --}}
    <div class="grid3">
        <div class="stat-widget">
            <div class="stat-widget-icon blue">
                <i class="fa-solid fa-folder-tree"></i>
            </div>
            <div class="stat-label">Total Keseluruhan</div>
            <div class="stat">{{ number_format($total) }}</div>
            <div class="stat-desc">Semua status unit rumah</div>
        </div>

        <div class="stat-widget" style="{{ $submitted > 0 ? 'border-color:#fde68a; background:#fffbeb;' : '' }}">
            <div class="stat-widget-icon amber">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div class="stat-label" style="{{ $submitted > 0 ? 'color:#b45309;' : '' }}">Menunggu Review</div>
            <div class="stat" style="{{ $submitted > 0 ? 'color:#b45309;' : '' }}">{{ number_format($submitted) }}</div>
            <div class="stat-desc">
                @if($submitted > 0)
                    <a href="{{ route('admin.review.index') }}" style="color:#b45309; font-weight:700; text-decoration:none;">
                        Buka antrian review <i class="fa-solid fa-arrow-right"></i>
                    </a>
                @else
                    Tidak ada antrian pending
                @endif
            </div>
        </div>

        <div class="stat-widget">
            <div class="stat-widget-icon" style="background:var(--slate-100); color:var(--slate-600);">
                <i class="fa-solid fa-file-pen"></i>
            </div>
            <div class="stat-label">Draft Surveyor</div>
            <div class="stat">{{ number_format($draft) }}</div>
            <div class="stat-desc">Sedang dilengkapi petugas</div>
        </div>
    </div>

    <div class="grid3" style="margin-top:20px;">
        <div class="stat-widget">
            <div class="stat-widget-icon purple">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="stat-label">Verified (Terverifikasi)</div>
            <div class="stat" style="color:#7c3aed;">{{ number_format($verified) }}</div>
            <div class="stat-desc">Siap untuk dipublikasikan ke portal</div>
        </div>

        <div class="stat-widget">
            <div class="stat-widget-icon emerald">
                <i class="fa-solid fa-globe"></i>
            </div>
            <div class="stat-label">Published (Publik)</div>
            <div class="stat" style="color:#059669;">{{ number_format($published) }}</div>
            <div class="stat-desc">Tampil di Peta GIS & Open Data</div>
        </div>

        <div class="stat-widget">
            <div class="stat-widget-icon rose">
                <i class="fa-solid fa-rotate-left"></i>
            </div>
            <div class="stat-label">Perlu Revisi</div>
            <div class="stat" style="color:#e11d48;">{{ number_format($revision) }}</div>
            <div class="stat-desc">Dikembalikan ke surveyor lapangan</div>
        </div>
    </div>

    {{-- QUICK ACTION HUB --}}
    <div class="card" style="margin-top: 24px; padding: 28px;">
        <h3 style="font-size: 18px; font-weight: 800; color: var(--slate-900); margin-bottom: 6px;">
            <i class="fa-solid fa-bolt text-amber-500" style="color:#f59e0b;"></i> Pintasan & Manajemen Utama
        </h3>
        <p class="muted" style="margin-bottom: 20px;">Akses cepat untuk memproses data dan meninjau modul sistem.</p>

        <div class="actions">
            <a class="btn primary" href="{{ route('admin.review.index') }}">
                <i class="fa-solid fa-clipboard-check"></i> Proses Review & Publikasi
            </a>

            <a class="btn" href="{{ route('houses.index') }}">
                <i class="fa-solid fa-list-check"></i> Kelola Semua Data RTLH
            </a>

            <a class="btn" href="{{ route('admin.surveyors.index') }}">
                <i class="fa-solid fa-users-gear"></i> Kelola Petugas Surveyor
            </a>

            <a class="btn" href="{{ route('regions.index') }}">
                <i class="fa-solid fa-map-pin"></i> Kelola Wilayah Administratif
            </a>

            <a class="btn" href="{{ route('surveyor.rtlh.index') }}">
                <i class="fa-solid fa-briefcase"></i> Mode Surveyor Lapangan
            </a>

            <a class="btn" href="{{ route('public.home') }}" target="_blank">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Cek Portal Publik
            </a>
        </div>
    </div>

</x-layouts.app>