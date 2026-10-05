<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::query()
            ->orderBy('level')
            ->orderBy('name')
            ->paginate(20);

        return view('regions.index', compact('regions'));
    }

    public function create()
    {
        $parents = Region::query()
            ->orderBy('level')
            ->orderBy('name')
            ->get();

        return view('regions.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:regions,id'],
            'code' => ['required', 'string', 'max:255', 'unique:regions,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:30'],
            'level' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Region::create($validated);

        return redirect()
            ->route('regions.index')
            ->with('success', 'Wilayah berhasil ditambahkan.');
    }
}