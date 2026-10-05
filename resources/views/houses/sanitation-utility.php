<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Sanitasi & Utilitas Rumah
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $house->house_code }}
                </p>
            </div>

            <a href="{{ route('houses.show', $house) }}"
               class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-300 bg-red-50 px-5 py-4">
                    <h3 class="font-semibold text-red-800 mb-2">
                        Terjadi kesalahan:
                    </h3>

                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('houses.sanitation-utility.update', $house) }}"
                  class="space-y-6">

                @csrf
                @method('PUT')

                {{-- SANITASI --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900">
                            Sanitasi
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Informasi sumber air dan fasilitas sanitasi rumah.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Sumber Air --}}
                        <div>
                            <label for="water_source_id"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                Sumber Air
                            </label>

                            <select name="water_source_id"
                                    id="water_source_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                                <option value="">-- Pilih Sumber Air --</option>

                                @foreach ($waterSources as $item)
                                    <option value="{{ $item->id }}"
                                        @selected(old(
                                            'water_source_id',
                                            $house->sanitation?->water_source_id
                                        ) == $item->id)>
                                        {{ $item->label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Toilet --}}
                        <div>
                            <label for="toilet_available"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                Ketersediaan Toilet
                            </label>

                            <select name="toilet_available"
                                    id="toilet_available"
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                                <option value="1"
                                    @selected(old(
                                        'toilet_available',
                                        $house->sanitation?->toilet_available ?? 0
                                    ) == 1)>
                                    Tersedia
                                </option>

                                <option value="0"
                                    @selected(old(
                                        'toilet_available',
                                        $house->sanitation?->toilet_available ?? 0
                                    ) == 0)>
                                    Tidak Tersedia
                                </option>
                            </select>
                        </div>

                        {{-- Jenis Toilet --}}
                        <div>
                            <label for="toilet_type_id"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Toilet
                            </label>

                            <select name="toilet_type_id"
                                    id="toilet_type_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                                <option value="">-- Pilih Jenis Toilet --</option>

                                @foreach ($toiletTypes as $item)
                                    <option value="{{ $item->id }}"
                                        @selected(old(
                                            'toilet_type_id',
                                            $house->sanitation?->toilet_type_id
                                        ) == $item->id)>
                                        {{ $item->label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pembuangan Tinja --}}
                        <div>
                            <label for="fecal_disposal_type_id"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                Pembuangan Tinja
                            </label>

                            <select name="fecal_disposal_type_id"
                                    id="fecal_disposal_type_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                                <option value="">-- Pilih Pembuangan Tinja --</option>

                                @foreach ($fecalDisposals as $item)
                                    <option value="{{ $item->id }}"
                                        @selected(old(
                                            'fecal_disposal_type_id',
                                            $house->sanitation?->fecal_disposal_type_id
                                        ) == $item->id)>
                                        {{ $item->label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Jarak Air ke Tinja --}}
                        <div class="md:col-span-2">
                            <label for="water_fecal_distance"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                Jarak Sumber Air ke Tempat Pembuangan Tinja
                            </label>

                            <input type="text"
                                   name="water_fecal_distance"
                                   id="water_fecal_distance"
                                   value="{{ old(
                                       'water_fecal_distance',
                                       $house->sanitation?->water_fecal_distance
                                   ) }}"
                                   placeholder="Contoh: 10 meter"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                    </div>
                </div>

                {{-- UTILITAS --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900">
                            Utilitas
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Informasi pencahayaan, ventilasi, dan sumber penerangan.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Bukaan Cahaya --}}
                        <div>
                            <label for="light_opening"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                Bukaan Cahaya
                            </label>

                            <input type="text"
                                   name="light_opening"
                                   id="light_opening"
                                   value="{{ old(
                                       'light_opening',
                                       $house->utility?->light_opening
                                   ) }}"
                                   placeholder="Contoh: Ada / Tidak Ada"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        {{-- Ventilasi --}}
                        <div>
                            <label for="ventilation"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                Ventilasi
                            </label>

                            <input type="text"
                                   name="ventilation"
                                   id="ventilation"
                                   value="{{ old(
                                       'ventilation',
                                       $house->utility?->ventilation
                                   ) }}"
                                   placeholder="Contoh: Ada / Tidak Ada"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        {{-- Sumber Penerangan --}}
                        <div class="md:col-span-2">
                            <label for="lighting_source_id"
                                   class="block text-sm font-medium text-gray-700 mb-2">
                                Sumber Penerangan
                            </label>

                            <select name="lighting_source_id"
                                    id="lighting_source_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                                <option value="">-- Pilih Sumber Penerangan --</option>

                                @foreach ($lightingSources as $item)
                                    <option value="{{ $item->id }}"
                                        @selected(old(
                                            'lighting_source_id',
                                            $house->utility?->lighting_source_id
                                        ) == $item->id)>
                                        {{ $item->label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>

                {{-- ACTION --}}
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('houses.show', $house) }}"
                       class="px-5 py-2.5 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Batal
                    </a>

                    <button type="submit"
                            class="px-5 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Simpan Data
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>