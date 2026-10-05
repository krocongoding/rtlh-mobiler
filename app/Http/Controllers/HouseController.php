<?php

namespace App\Http\Controllers;

use App\Models\MasterCategory;
use App\Models\House;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class HouseController extends Controller
{
    public function index(Request $request)
    {
        $query = House::with('region')
            ->latest('id');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($q) use ($search) {
                $q->where('house_code', 'ilike', "%{$search}%")
                    ->orWhere('address', 'ilike', "%{$search}%");
            });
        }

        $houses = $query
            ->paginate(15)
            ->withQueryString();

        return view('houses.index', compact('houses'));
    }

   public function create()
    {
    $districts = \App\Models\Region::where('level', 1)
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    $villages = \App\Models\Region::where('level', 2)
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    return view('houses.create', compact('districts', 'villages'));
    }

    public function store(Request $request)
    {
         $validated = $request->validate([
            'region_id' => ['required', 'exists:regions,id'],
            'house_code' => ['required', 'string', 'max:50', 'unique:houses,house_code'],
            'address' => ['nullable', 'string'],
            'block' => ['nullable', 'string', 'max:100'],
            'rt' => ['nullable', 'string', 'max:10'],
            'rw' => ['nullable', 'string', 'max:10'],
            'area_m2' => ['nullable', 'numeric', 'min:0'],
            'occupant_count' => ['nullable', 'integer', 'min:0'],
            'household_count' => ['nullable', 'integer', 'min:0'],
            'survey_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            ]);

        $validated['status'] = 'draft';
        $validated['is_public'] = false;

        $house = House::create($validated);

        return redirect()
            ->route('houses.show', $house)
            ->with('success', 'Data RTLH berhasil ditambahkan.');
    }

public function edit(House $house)
{
    $villages = \App\Models\Region::where('level', 2)
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    return view('houses.edit', compact('house', 'villages'));
}

public function destroy(House $house)
{
    Gate::authorize('delete', $house);

    $house->delete();

    return redirect()
        ->route('houses.index')
        ->with('success', 'Data RTLH berhasil dihapus.');
}

public function update(Request $request, House $house)
{
    $validated = $request->validate([
        'region_id' => ['required', 'exists:regions,id'],
        'house_code' => [
            'required',
            'string',
            'max:50',
            'unique:houses,house_code,' . $house->id,
        ],
        'address' => ['nullable', 'string'],
        'block' => ['nullable', 'string', 'max:100'],
        'rt' => ['nullable', 'string', 'max:10'],
        'rw' => ['nullable', 'string', 'max:10'],
        'area_m2' => ['nullable', 'numeric', 'min:0'],
        'occupant_count' => ['nullable', 'integer', 'min:0'],
        'household_count' => ['nullable', 'integer', 'min:0'],
        'survey_year' => [
            'required',
            'integer',
            'min:2000',
            'max:2100',
        ],
    ]);

    $house->update($validated);

    return redirect()
        ->route('houses.show', $house)
        ->with('success', 'Data RTLH berhasil diperbarui.');
}

    public function show(House $house)
    {
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
        'occupants',
        'photos',
    ]);

    $assessmentCategories = MasterCategory::with([
    'values' => function ($query) {
        $query->where('is_active', true)
            ->orderBy('sort_order');
    }
        ])
        ->where('is_active', true)
        ->orderBy('id')
        ->get();

        return view('houses.show', compact(
            'house',
            'assessmentCategories'
            ));
    }
}