<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Portal RTLH Kabupaten Cirebon</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900">

    <div class="min-h-screen">

        <header class="bg-white border-b">
            <div class="max-w-7xl mx-auto px-4 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">
                            Portal RTLH Kabupaten Cirebon
                        </h1>

                        <p class="text-sm text-gray-500 mt-1">
                            Data Rumah Tidak Layak Huni
                        </p>
                    </div>

                    <a
                        href="{{ route('login') }}"
                        class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm"
                    >
                        Login
                    </a>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 py-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

                <div class="bg-white border rounded-xl p-5">
                    <p class="text-sm text-gray-500">
                        Total RTLH Publik
                    </p>

                    <p class="text-3xl font-bold mt-2">
                        {{ number_format($totalPublicHouses) }}
                    </p>
                </div>

            </div>

            <div class="bg-white border rounded-xl overflow-hidden">

                <div class="p-5 border-b flex items-center justify-between">

                    <div>
                        <h2 class="font-semibold">
                            Data RTLH
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Data yang telah ditetapkan sebagai data publik.
                        </p>
                    </div>

                    <a
                        href="{{ route('public.rtlh.download') }}"
                        class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm"
                    >
                        Download Data
                    </a>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="text-left px-5 py-3">Kode</th>
                                <th class="text-left px-5 py-3">Wilayah</th>
                                <th class="text-left px-5 py-3">Alamat</th>
                                <th class="text-left px-5 py-3">Tahun</th>
                                <th class="text-right px-5 py-3">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">

                            @forelse($houses as $house)

                                <tr>

                                    <td class="px-5 py-4">
                                        {{ $house->house_code }}
                                    </td>

                                    <td class="px-5 py-4">
                                        {{ $house->region?->name ?? '-' }}
                                    </td>

                                    <td class="px-5 py-4">
                                        {{ $house->address ?? '-' }}
                                    </td>

                                    <td class="px-5 py-4">
                                        {{ $house->survey_year ?? '-' }}
                                    </td>

                                    <td class="px-5 py-4 text-right">
                                        <a
                                            href="{{ route('public.rtlh.show', $house) }}"
                                            class="text-blue-600 hover:underline"
                                        >
                                            Lihat
                                        </a>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="5"
                                        class="px-5 py-10 text-center text-gray-500"
                                    >
                                        Belum ada data RTLH publik.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="p-5 border-t">
                    {{ $houses->links() }}
                </div>

            </div>

        </main>

    </div>

</body>
</html> 