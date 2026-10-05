<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\MasterCategory;
use App\Models\RtlhAssessment;
use App\Models\RtlhAssessmentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HouseAssessmentController extends Controller
{
    public function edit(House $house)
    {
        $assessment = RtlhAssessment::with('items')
            ->where('house_id', $house->id)
            ->latest('assessment_year')
            ->latest('id')
            ->first();

        $categories = MasterCategory::with([
            'values' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('sort_order');
            }
        ])
        ->where('is_active', true)
        ->orderBy('id')
        ->get();

        return view('houses.assessment', compact(
            'house',
            'assessment',
            'categories'
        ));
    }

    public function update(Request $request, House $house)
    {
        $validated = $request->validate([
            'assessment_year' => [
                'required',
                'integer',
                'min:2000',
                'max:2100',
            ],

            'assessment_date' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                'string',
                'max:50',
            ],

            'score' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'priority_level' => [
                'nullable',
                'string',
                'max:50',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

            'items' => [
                'nullable',
                'array',
            ],

            'items.*.category' => [
                'required',
                'string',
                'max:100',
            ],

            'items.*.item_code' => [
                'required',
                'string',
                'max:100',
            ],

            'items.*.value' => [
                'nullable',
                'string',
                'max:100',
            ],

            'items.*.other_value' => [
                'nullable',
                'string',
                'max:255',
            ],

            'items.*.score' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'items.*.notes' => [
                'nullable',
                'string',
            ],
        ]);

        DB::transaction(function () use ($house, $validated) {

            $assessment = RtlhAssessment::updateOrCreate(
                [
                    'house_id' => $house->id,
                    'assessment_year' => $validated['assessment_year'],
                ],
                [
                    'assessment_date' => $validated['assessment_date'],
                    'assessor_id' => auth()->id(),
                    'status' => $validated['status'],
                    'score' => $validated['score'] ?? null,
                    'priority_level' => $validated['priority_level'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ]
            );

            /*
             * Karena detail assessment adalah snapshot
             * dari pilihan saat disimpan, kita replace
             * item-item assessment ini.
             */
            RtlhAssessmentItem::where(
                'assessment_id',
                $assessment->id
            )->delete();

            foreach ($validated['items'] ?? [] as $item) {

                $value = $item['value'] ?? null;

                /*
                 * other_value hanya disimpan kalau
                 * pilihan yang dipilih adalah OTHER.
                 */
                $otherValue = strtoupper($value ?? '') === 'OTHER'
                    ? ($item['other_value'] ?? null)
                    : null;

                RtlhAssessmentItem::create([
                    'assessment_id' => $assessment->id,
                    'category' => $item['category'],
                    'item_code' => $item['item_code'],
                    'value' => $value,
                    'other_value' => $otherValue,
                    'score' => $item['score'] ?? null,
                    'notes' => $item['notes'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('houses.show', $house)
            ->with(
                'success',
                'Assessment RTLH berhasil disimpan.'
            );
    }
}

