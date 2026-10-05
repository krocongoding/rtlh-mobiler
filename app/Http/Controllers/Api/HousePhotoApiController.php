<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\House;
use App\Services\HouseDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HousePhotoApiController extends Controller
{
    /**
     * Upload satu atau lebih foto rumah.
     *
     * POST /api/v1/surveyor/houses/{house}/photos
     *
     * Field yang diterima (multipart/form-data):
     *   photo_front, photo_angle, photo_side, photo_back,
     *   photo_family_room, photo_bathroom
     *
     * Setiap field adalah file gambar (jpg/png/webp, max 5MB).
     */
    public function store(
        Request $request,
        House $house,
        HouseDataService $service
    ): JsonResponse {
        // Pastikan surveyor hanya upload ke rumah miliknya
        if (! $request->user()->hasRole('admin')) {
            abort_unless(
                $house->created_by === $request->user()->id,
                403,
                'Anda tidak memiliki akses ke data ini.'
            );
        }

        $photoFields = [
            'photo_front',
            'photo_angle',
            'photo_side',
            'photo_back',
            'photo_family_room',
            'photo_bathroom',
        ];

        // Validasi: setidaknya satu foto harus ada
        $request->validate([
            'photo_front'       => ['nullable', 'image', 'max:5120'],
            'photo_angle'       => ['nullable', 'image', 'max:5120'],
            'photo_side'        => ['nullable', 'image', 'max:5120'],
            'photo_back'        => ['nullable', 'image', 'max:5120'],
            'photo_family_room' => ['nullable', 'image', 'max:5120'],
            'photo_bathroom'    => ['nullable', 'image', 'max:5120'],
        ]);

        $hasPhoto = collect($photoFields)
            ->some(fn ($f) => $request->hasFile($f));

        if (! $hasPhoto) {
            return response()->json([
                'message' => 'Tidak ada foto yang dikirim.',
            ], 422);
        }

        $service->storePhotos($house, $request->allFiles(), $request->user()->id);

        // Kembalikan URL foto terbaru
        $house->load('photos');
        $photos = $house->photos->map(fn ($p) => [
            'id'   => $p->id,
            'type' => $p->type,
            'url'  => $p->path ? asset('storage/' . $p->path) : null,
        ])->values();

        return response()->json([
            'message' => 'Foto berhasil diupload.',
            'data'    => $photos,
        ]);
    }

    /**
     * Hapus satu foto.
     *
     * DELETE /api/v1/surveyor/houses/{house}/photos/{photo}
     */
    public function destroy(
        Request $request,
        House $house,
        int $photoId
    ): JsonResponse {
        if (! $request->user()->hasRole('admin')) {
            abort_unless(
                $house->created_by === $request->user()->id,
                403,
                'Anda tidak memiliki akses ke data ini.'
            );
        }

        $photo = $house->photos()->findOrFail($photoId);

        if ($photo->path && Storage::disk('public')->exists($photo->path)) {
            Storage::disk('public')->delete($photo->path);
        }

        $photo->delete();

        return response()->json([
            'message' => 'Foto berhasil dihapus.',
        ]);
    }
}
