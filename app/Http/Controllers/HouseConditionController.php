<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\HouseFloor;
use App\Models\HouseRoof;
use App\Models\HouseStructure;
use App\Models\HouseWall;
use App\Models\MasterCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HouseConditionController extends Controller
{
    public function edit(House $house)
    {
        $condition = MasterCategory::where('code', 'HOUSE_CONDITION')
            ->firstOrFail();

        $floorMaterial = MasterCategory::where('code', 'FLOOR_MATERIAL')
            ->firstOrFail();

        $wallMaterial = MasterCategory::where('code', 'WALL_MATERIAL')
            ->firstOrFail();

        $roofMaterial = MasterCategory::where('code', 'ROOF_MATERIAL')
            ->firstOrFail();

        $conditions = $condition->values()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $floorMaterials = $floorMaterial->values()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $wallMaterials = $wallMaterial->values()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $roofMaterials = $roofMaterial->values()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $house->load([
            'structure',
            'floor',
            'wall',
            'roof',
        ]);

        return view('houses.condition', compact(
            'house',
            'conditions',
            'floorMaterials',
            'wallMaterials',
            'roofMaterials'
        ));
    }

    public function update(Request $request, House $house)
    {
        $validated = $request->validate([
            'foundation' => ['nullable', 'string', 'max:255'],

            'sloof_condition_id' => [
                'nullable',
                'exists:master_values,id',
            ],

            'column_condition_id' => [
                'nullable',
                'exists:master_values,id',
            ],

            'beam_condition_id' => [
                'nullable',
                'exists:master_values,id',
            ],

            'floor_material_id' => [
                'nullable',
                'exists:master_values,id',
            ],

            'floor_condition_id' => [
                'nullable',
                'exists:master_values,id',
            ],

            'wall_material_id' => [
                'nullable',
                'exists:master_values,id',
            ],

            'wall_condition_id' => [
                'nullable',
                'exists:master_values,id',
            ],

            'roof_frame_condition_id' => [
                'nullable',
                'exists:master_values,id',
            ],

            'roof_material_id' => [
                'nullable',
                'exists:master_values,id',
            ],

            'roof_condition_id' => [
                'nullable',
                'exists:master_values,id',
            ],
        ]);

        DB::transaction(function () use ($house, $validated) {

            HouseStructure::updateOrCreate(
                ['house_id' => $house->id],
                [
                    'foundation' => $validated['foundation'] ?? null,
                    'sloof_condition_id' => $validated['sloof_condition_id'] ?? null,
                    'column_condition_id' => $validated['column_condition_id'] ?? null,
                    'beam_condition_id' => $validated['beam_condition_id'] ?? null,
                ]
            );

            HouseFloor::updateOrCreate(
                ['house_id' => $house->id],
                [
                    'material_id' => $validated['floor_material_id'] ?? null,
                    'condition_id' => $validated['floor_condition_id'] ?? null,
                ]
            );

            HouseWall::updateOrCreate(
                ['house_id' => $house->id],
                [
                    'material_id' => $validated['wall_material_id'] ?? null,
                    'condition_id' => $validated['wall_condition_id'] ?? null,
                ]
            );

            HouseRoof::updateOrCreate(
                ['house_id' => $house->id],
                [
                    'frame_condition_id' => $validated['roof_frame_condition_id'] ?? null,
                    'material_id' => $validated['roof_material_id'] ?? null,
                    'condition_id' => $validated['roof_condition_id'] ?? null,
                ]
            );
        });

        return redirect()
            ->route('houses.show', $house)
            ->with('success', 'Kondisi fisik rumah berhasil disimpan.');
    }
}   