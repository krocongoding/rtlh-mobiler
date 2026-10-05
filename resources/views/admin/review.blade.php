<x-layouts.app title="Review & Verifikasi RTLH">

    <div class="pagehead">
        <div>
            <div class="kicker">
                <i class="fa-solid fa-clipboard-check"></i> MODUL REVIEW & APPROVAL
            </div>
            <h1>Antrian Review & Publikasi RTLH</h1>
            <p class="muted">
                Verifikasi keabsahan data lapangan dari surveyor sebelum dipublikasikan ke portal geospasial publik.
            </p>
        </div>

        <div class="actions">
            <a href="{{ route('admin.dashboard') }}" class="btn">
                <i class="fa-solid fa-arrow-left"></i> Dashboard Admin
            </a>
            <a href="{{ route('houses.index') }}" class="btn">
                <i class="fa-solid fa-list"></i> Semua Data
            </a>
        </div>
    </div>

    {{-- TABLE DATA REVIEW --}}
    <div class="card" style="padding:0; overflow:hidden;">
        <div style="padding: 20px 24px; border-bottom: 1px solid var(--slate-200); background: var(--slate-50); display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3 style="font-size: 17px; font-weight: 800; color: var(--slate-900); margin-bottom: 2px;">
                    Antrian Data Menunggu Keputusan
                </h3>
                <p class="muted" style="font-size: 13px;">Tinjau kelengkapan kondisi fisik, foto, titik koordinat, dan skor prioritas.</p>
            </div>

            <span class="badge" style="background:#fef3c7; color:#b45309; font-weight:700;">
                {{ $houses->total() }} Data Antrian
            </span>
        </div>

        <div class="tablewrap" style="border:none; border-radius:0;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 150px;">Kode RTLH</th>
                        <th>Wilayah / Kecamatan</th>
                        <th>Tahun</th>
                        <th>Status Saat Ini</th>
                        <th>Petugas Surveyor</th>
                        <th style="text-align: right; width: 280px;">Aksi Keputusan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($houses as $house)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div class="brand-logo" style="width:28px; height:28px; font-size:12px; border-radius:6px; flex-shrink:0;">
                                        <img src="{{ asset('images/logo.png') }}" alt="Logo RTLH">
                                    </div>
                                    <strong style="font-family:monospace; font-size:14px; color:var(--slate-900);">
                                        {{ $house->house_code }}
                                    </strong>
                                </div>
                            </td>

                            <td>
                                <span class="badge" style="background:var(--slate-100); color:var(--slate-700);">
                                    <i class="fa-solid fa-location-dot"></i> {{ $house->region?->name ?? '-' }}
                                </span>
                            </td>

                            <td>
                                <span class="badge" style="background:#e0f2fe; color:#0369a1;">
                                    {{ $house->survey_year ?? '-' }}
                                </span>
                            </td>

                            <td>
                                @if($house->status === 'submitted')
                                    <span class="badge submitted">
                                        <i class="fa-solid fa-hourglass-half"></i> Menunggu Review
                                    </span>
                                @elseif($house->status === 'verified')
                                    <span class="badge" style="background:#ede9fe; color:#6d28d9;">
                                        <i class="fa-solid fa-circle-check"></i> Terverifikasi (Siap Publish)
                                    </span>
                                @else
                                    <span class="badge">
                                        {{ ucfirst($house->status) }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                <div style="font-size:13px; font-weight:700; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
                                    <div class="user-avatar" style="width:26px; height:26px; font-size:11px; flex-shrink:0;">
                                        {{ strtoupper(substr($house->creator?->name ?? 'P', 0, 1)) }}
                                    </div>
                                    <div>
                                        @if($house->creator)
                                            <a href="{{ route('admin.surveyors.show', $house->creator) }}" style="color:var(--slate-900); text-decoration:none;" title="Lihat Log History Surveyor">
                                                {{ $house->creator->name }}
                                                <span class="badge" style="background:var(--slate-100); color:var(--slate-600); font-family:monospace; font-size:10px; margin-left:2px;">
                                                    #{{ $house->creator->id }}
                                                </span>
                                            </a>
                                        @else
                                            <span class="muted">Petugas #{{ $house->created_by ?? '-' }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td style="text-align: right;">
                                <div class="actions" style="justify-content: flex-end; gap:6px;">
                                    {{-- DETAIL --}}
                                    <a href="{{ route('houses.show', $house) }}" class="btn small" title="Tinjau Detail">
                                        <i class="fa-solid fa-eye"></i> Detail
                                    </a>

                                    @if($house->status === 'submitted')
                                        {{-- VERIFY --}}
                                        <form method="POST" action="{{ route('admin.review.verify', $house) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn small primary" title="Verifikasi Data">
                                                <i class="fa-solid fa-check"></i> Verifikasi
                                            </button>
                                        </form>

                                        {{-- REVISION --}}
                                        <form method="POST" action="{{ route('admin.review.revision', $house) }}" style="display:inline;" onsubmit="return confirm('Kembalikan data ini ke surveyor untuk revisi?')">
                                            @csrf
                                            <button type="submit" class="btn small danger" title="Minta Revisi">
                                                <i class="fa-solid fa-rotate-left"></i> Revisi
                                            </button>
                                        </form>

                                    @elseif($house->status === 'verified')
                                        {{-- PUBLISH --}}
                                        <form method="POST" action="{{ route('admin.review.publish', $house) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn small" style="background:#059669; color:#fff; border-color:#059669;" title="Terbitkan ke Peta Publik">
                                                <i class="fa-solid fa-globe"></i> Publish
                                            </button>
                                        </form>

                                        {{-- REVISION --}}
                                        <form method="POST" action="{{ route('admin.review.revision', $house) }}" style="display:inline;" onsubmit="return confirm('Kembalikan data ini ke surveyor untuk revisi?')">
                                            @csrf
                                            <button type="submit" class="btn small danger" title="Minta Revisi">
                                                <i class="fa-solid fa-rotate-left"></i> Revisi
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 48px 24px;">
                                <div style="width:56px; height:56px; border-radius:50%; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; font-size:24px; margin:0 auto 14px;">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <h4 style="font-size:16px; font-weight:700; color:var(--slate-800); margin-bottom:4px;">
                                    Semua Data Bersih & Terverifikasi
                                </h4>
                                <p class="muted" style="font-size:13px; max-width:400px; margin:0 auto;">
                                    Tidak ada data baru yang sedang menunggu antrian review verifikasi saat ini.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($houses->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid var(--slate-200); background: var(--slate-50); display:flex; justify-content:space-between; align-items:center;">
                <div class="muted" style="font-size:13px;">
                    Halaman {{ $houses->currentPage() }} dari {{ $houses->lastPage() }}
                </div>
                <div>
                    {{ $houses->links() }}
                </div>
            </div>
        @endif
    </div>

</x-layouts.app>