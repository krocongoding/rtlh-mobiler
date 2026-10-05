<?php

namespace App\Http\Controllers;

use App\Models\House;
use Illuminate\Http\Response;

class PublicRtlhController extends Controller
{
    public function index()
    {
        $houses = House::query()
            ->with('region')
            ->where('is_public', true)
            ->latest('id')
            ->paginate(20);

        $totalPublicHouses = House::where('is_public', true)->count();

        $housesByYear = House::query()
            ->where('is_public', true)
            ->whereNotNull('survey_year')
            ->selectRaw('survey_year, COUNT(*) as total')
            ->groupBy('survey_year')
            ->orderByDesc('survey_year')
            ->get();

        return view('public.rtlh.index', compact(
            'houses',
            'totalPublicHouses',
            'housesByYear'
        ));
    }

    public function show(House $house)
    {
        abort_unless($house->is_public, 404);

        $house->load([
            'region',
            'assessments.items',
            'structure.sloofCondition',
            'structure.columnCondition',
            'structure.beamCondition',
            'floor.material',
            'floor.condition',
            'wall.material',
            'wall.condition',
            'roof.frameCondition',
            'roof.material',
            'roof.condition',
            'sanitation.waterSource',
            'sanitation.toiletType',
            'sanitation.fecalDisposalType',
            'utility.lightingSource',
            'photos',
        ]);

        return view('public.rtlh.show', compact('house'));
    }

    public function download()
    {
        $houses = House::query()
            ->with('region')
            ->where('is_public', true)
            ->orderBy('id')
            ->get();

        return response()->streamDownload(function () use ($houses) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Kode Rumah',
                'Kecamatan/Desa',
                'Alamat',
                'Blok',
                'RT',
                'RW',
                'Luas Bangunan',
                'Jumlah Penghuni',
                'Jumlah KK',
                'Tahun Survei',
                'Status',
            ]);

            foreach ($houses as $house) {
                fputcsv($handle, [
                    $house->house_code,
                    $house->region?->name,
                    $house->address,
                    $house->block,
                    $house->rt,
                    $house->rw,
                    $house->area_m2,
                    $house->occupant_count,
                    $house->household_count,
                    $house->survey_year,
                    $house->status,
                ]);
            }

            fclose($handle);
        }, 'data-rtlh-cirebon.csv');
    }
}