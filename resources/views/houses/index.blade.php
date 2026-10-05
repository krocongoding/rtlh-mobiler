<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="kicker">
                <i class="fa-solid fa-folder-tree"></i> BASIS DATA INTERNAL
            </div>
            <h1>Manajemen Data RTLH</h1>
            <p class="muted">
                Daftar lengkap rumah tidak layak huni Kabupaten Cirebon beserta status verifikasi dan survei.
            </p>
        </div>

        <div class="actions">
            <a href="{{ route('houses.create') }}" class="btn primary">
                <i class="fa-solid fa-plus"></i> Tambah Data Rumah
            </a>
        </div>
    </x-slot>

    {{-- SEARCH & FILTER CARD --}}
    <div class="card" style="margin-bottom: 24px;">
        <form method="GET" action="{{ route('houses.index') }}">
            <div class="toolbar">
                <div style="flex:1; min-width:260px; position:relative;">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari kode rumah, alamat, atau blok..."
                        style="padding-left:36px;"
                    >
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--slate-400);"></i>
                </div>

                <button type="submit" class="btn primary">
                    <i class="fa-solid fa-magnifying-glass"></i> Cari
                </button>

                @if(request('search'))
                    <a href="{{ route('houses.index') }}" class="btn">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- TABLE DATA CARD --}}
    <div class="card" style="padding:0; overflow:hidden;">
        <div style="padding: 18px 24px; border-bottom: 1px solid var(--slate-200); background: var(--slate-50); display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3 style="font-size:16px; font-weight:800; color:var(--slate-900); margin:0;">
                    Daftar Unit Rumah
                </h3>
            </div>
            <span class="badge published">
                Total {{ number_format($houses->total()) }} Unit
            </span>
        </div>

        <div class="tablewrap" style="border:none; border-radius:0;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 150px;">Kode Rumah</th>
                        <th>Wilayah</th>
                        <th>Alamat / Blok</th>
                        <th>Tahun</th>
                        <th>Status</th>
                        <th style="text-align: right; width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($houses as $house)
                        <tr>
                            <td class="muted">{{ $houses->firstItem() + $loop->index }}</td>
                            <td>
                                <strong style="font-family:monospace; font-size:14px; color:var(--slate-900);">
                                    {{ $house->house_code }}
                                </strong>
                            </td>
                            <td>
                                <span class="badge" style="background:var(--slate-100); color:var(--slate-700);">
                                    <i class="fa-solid fa-location-dot"></i> {{ $house->region?->name ?? '-' }}
                                </span>
                            </td>
                            <td>
                                {{ $house->address ?: ($house->block ? 'Blok ' . $house->block : '-') }}
                            </td>
                            <td>
                                <span class="badge" style="background:#e0f2fe; color:#0369a1;">
                                    {{ $house->survey_year ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @if($house->status === 'published')
                                    <span class="badge published"><i class="fa-solid fa-check"></i> Published</span>
                                @elseif($house->status === 'verified')
                                    <span class="badge" style="background:#ede9fe; color:#6d28d9;"><i class="fa-solid fa-circle-check"></i> Verified</span>
                                @elseif($house->status === 'submitted')
                                    <span class="badge submitted"><i class="fa-solid fa-hourglass-half"></i> Submitted</span>
                                @elseif($house->status === 'revision')
                                    <span class="badge revision"><i class="fa-solid fa-rotate-left"></i> Revisi</span>
                                @else
                                    <span class="badge draft">{{ ucfirst($house->status) }}</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('houses.show', $house) }}" class="btn small primary">
                                    <i class="fa-solid fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:48px 24px;" class="muted">
                                <i class="fa-solid fa-folder-open fa-2x" style="color:var(--slate-300); margin-bottom:10px;"></i>
                                <p>Belum ada data unit rumah RTLH yang tercatat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($houses->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan
            <strong>{{ $houses->firstItem() }}</strong>
            -
            <strong>{{ $houses->lastItem() }}</strong>
            dari
            <strong>{{ $houses->total() }}</strong>
        </div>

        <nav class="pagination" aria-label="Pagination">

            {{-- Previous --}}
            @if ($houses->onFirstPage())
                <span class="page-btn disabled">
                    <i class="fa-solid fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $houses->previousPageUrl() }}" class="page-btn">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            @endif

            {{-- Nomor halaman --}}
            @foreach ($houses->getUrlRange(
                max(1, $houses->currentPage() - 2),
                min($houses->lastPage(), $houses->currentPage() + 2)
            ) as $page => $url)

                @if ($page == $houses->currentPage())
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
            @if ($houses->hasMorePages())
                <a href="{{ $houses->nextPageUrl() }}" class="page-btn">
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