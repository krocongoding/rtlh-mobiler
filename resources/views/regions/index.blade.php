<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="kicker">
                <i class="fa-solid fa-map-pin"></i> DATA MASTER
            </div>
            <h1>Wilayah Administratif</h1>
            <p class="muted">
                Daftar kecamatan dan desa/kelurahan di lingkungan Pemerintah Kabupaten Cirebon.
            </p>
        </div>

        <div class="actions">
            <a href="{{ route('regions.create') }}" class="btn primary">
                <i class="fa-solid fa-plus"></i> Tambah Wilayah
            </a>
        </div>
    </x-slot>

    <div class="card" style="padding:0; overflow:hidden;">
        <div style="padding: 18px 24px; border-bottom: 1px solid var(--slate-200); background: var(--slate-50); display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3 style="font-size:16px; font-weight:800; color:var(--slate-900); margin:0;">
                    Tabel Wilayah
                </h3>
            </div>
            <span class="badge published">
                Total {{ number_format($regions->total()) }} Wilayah
            </span>
        </div>

        <div class="tablewrap" style="border:none; border-radius:0;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 140px;">Kode Wilayah</th>
                        <th>Nama Wilayah</th>
                        <th>Tipe</th>
                        <th>Level</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($regions as $region)
                        <tr>
                            <td class="muted">{{ $regions->firstItem() + $loop->index }}</td>
                            <td>
                                <code style="background:var(--slate-100); padding:2px 6px; border-radius:4px; font-size:12px; font-weight:700;">{{ $region->code }}</code>
                            </td>
                            <td>
                                <strong style="color:var(--slate-900);">{{ $region->name }}</strong>
                            </td>
                            <td>
                                <span class="badge" style="background:var(--slate-100); color:var(--slate-700);">
                                    {{ ucfirst($region->type) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge" style="background:#e0f2fe; color:#0369a1;">
                                    Level {{ $region->level }}
                                </span>
                            </td>
                            <td>
                                @if($region->is_active)
                                    <span class="badge published"><i class="fa-solid fa-circle-check"></i> Aktif</span>
                                @else
                                    <span class="badge draft">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:48px 24px;" class="muted">
                                Belum ada data wilayah.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($regions->hasPages())
    <div class="pagination-wrap">

        <div class="pagination-info">
            Halaman
            <strong>{{ $regions->currentPage() }}</strong>
            dari
            <strong>{{ $regions->lastPage() }}</strong>
        </div>

        <nav class="pagination" aria-label="Pagination">

            {{-- Previous --}}
            @if ($regions->onFirstPage())
                <span class="page-btn disabled">
                    <i class="fa-solid fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $regions->previousPageUrl() }}" class="page-btn">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif

            {{-- Page numbers --}}
            @foreach ($regions->getUrlRange(
                max(1, $regions->currentPage() - 2),
                min($regions->lastPage(), $regions->currentPage() + 2)
            ) as $page => $url)

                @if ($page == $regions->currentPage())
                    <span class="page-number active">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}" class="page-number">
                        {{ $page }}
                    </a>
                @endif

            @endforeach

            {{-- Next --}}
            @if ($regions->hasMorePages())
                <a href="{{ $regions->nextPageUrl() }}" class="page-btn">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            @else
                <span class="page-btn disabled">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
            @endif

        </nav>
    </div>
@endif
    </div>
</x-app-layout>