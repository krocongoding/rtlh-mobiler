<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\HouseOccupant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HouseOccupantController extends Controller
{
    public function edit(House $house)
    {
        $house->load('occupants');

        return view('houses.occupants', compact('house'));
    }

    public function update(Request $request, House $house)
    {
        $validated = $request->validate([
            'occupants' => ['nullable', 'array'],

            'occupants.*.id' => [
                'nullable',
                'integer',
                'exists:house_occupants,id',
            ],

            'occupants.*.relationship' => [
                'required',
                'string',
                'max:100',
            ],

            'occupants.*.gender' => [
                'required',
                'string',
                'max:20',
            ],

            'occupants.*.birth_year' => [
                'nullable',
                'integer',
                'min:1900',
                'max:2100',
            ],

            'occupants.*.occupation' => [
                'nullable',
                'string',
                'max:255',
            ],

            'occupants.*.education' => [
                'nullable',
                'string',
                'max:255',
            ],

            'occupants.*.is_primary_contact' => [
                'nullable',
                'boolean',
            ],
        ]);

        DB::transaction(function () use ($house, $validated) {

            $submittedIds = [];

            foreach ($validated['occupants'] ?? [] as $occupantData) {

                $occupantId = $occupantData['id'] ?? null;

                $data = [
                    'house_id' => $house->id,
                    'relationship' => $occupantData['relationship'],
                    'gender' => $occupantData['gender'],
                    'birth_year' => $occupantData['birth_year'] ?? null,
                    'occupation' => $occupantData['occupation'] ?? null,
                    'education' => $occupantData['education'] ?? null,
                    'is_primary_contact' => !empty(
                        $occupantData['is_primary_contact']
                    ),
                ];

                if ($occupantId) {

                    $occupant = HouseOccupant::where('id', $occupantId)
                        ->where('house_id', $house->id)
                        ->first();

                    if ($occupant) {
                        $occupant->update($data);
                        $submittedIds[] = $occupant->id;
                    }

                } else {

                    $occupant = HouseOccupant::create($data);

                    $submittedIds[] = $occupant->id;
                }
            }

            // Hapus penghuni yang sudah tidak ada di form
            $house->occupants()
                ->whereNotIn('id', $submittedIds)
                ->delete();
        });

        return redirect()
            ->route('houses.show', $house)
            ->with(
                'success',
                'Data penghuni rumah berhasil disimpan.'
            );
    }
}