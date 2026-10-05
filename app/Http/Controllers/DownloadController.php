<?php

namespace App\Http\Controllers;

use App\Models\House;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    public function csv(Request $request): StreamedResponse
    {
        $query = House::query()
            ->with([
                'region',
                'structure',
                'floor',
                'wall',
                'roof',
                'sanitation',
                'utility',
            ])
            ->where('is_public', true)
            ->where('status', 'published');

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim(
                $request->input('search')
            );

            $query->where(
                'house_code',
                'ilike',
                '%' . $search . '%'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter tahun
        |--------------------------------------------------------------------------
        */

        if ($request->filled('year')) {

            $query->where(
                'survey_year',
                $request->integer('year')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter wilayah
        |--------------------------------------------------------------------------
        */

        if ($request->filled('region_id')) {

            $query->where(
                'region_id',
                $request->integer('region_id')
            );
        }

        return response()->streamDownload(
            function () use ($query) {

                $output = fopen(
                    'php://output',
                    'w'
                );

                /*
                | UTF-8 BOM supaya Excel membaca UTF-8 dengan benar.
                */

                fwrite(
                    $output,
                    "\xEF\xBB\xBF"
                );

                fputcsv($output, [
                    'house_code',
                    'wilayah',
                    'luas_m2',
                    'tahun',
                    'status',
                    'sloof',
                    'kolom',
                    'balok',
                    'lantai',
                    'dinding',
                    'atap',
                    'jamban',
                    'air_minum',
                    'penerangan',
                ]);

                foreach ($query->cursor() as $house) {

                    fputcsv($output, [

                        $house->house_code,

                        $house->region?->name,

                        $house->area_m2,

                        $house->survey_year,

                        $house->status,

                        $house->structure?->sloof_condition_id,

                        $house->structure?->column_condition_id,

                        $house->structure?->beam_condition_id,

                        $house->floor?->condition_id,

                        $house->wall?->condition_id,

                        $house->roof?->condition_id,

                        $house->sanitation?->toilet_available
                            ? 'ADA'
                            : 'TIDAK PUNYA',

                        $house->sanitation?->water_source_id,

                        $house->utility?->lighting_source_id,

                    ]);
                }

                fclose($output);
            },

            'rtlh-cirebon.csv',

            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',
            ]
        );
    }
}