<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $kabupaten = Region::where('code', '32.09')->firstOrFail();

        $regions = [
            ['32.09.01', 'Waled'],
            ['32.09.02', 'Ciledug'],
            ['32.09.03', 'Losari'],
            ['32.09.04', 'Pabedilan'],
            ['32.09.05', 'Babakan'],
            ['32.09.06', 'Karangsembung'],
            ['32.09.07', 'Lemahabang'],
            ['32.09.08', 'Susukan Lebak'],
            ['32.09.09', 'Sedong'],
            ['32.09.10', 'Astanajapura'],
            ['32.09.11', 'Pangenan'],
            ['32.09.12', 'Mundu'],
            ['32.09.13', 'Beber'],
            ['32.09.14', 'Talun'],
            ['32.09.15', 'Sumber'],
            ['32.09.16', 'Dukupuntang'],
            ['32.09.17', 'Palimanan'],
            ['32.09.18', 'Plumbon'],
            ['32.09.19', 'Weru'],
            ['32.09.20', 'Kedawung'],
            ['32.09.21', 'Gunung Jati'],
            ['32.09.22', 'Kapetakan'],
            ['32.09.23', 'Klangenan'],
            ['32.09.24', 'Arjawinangun'],
            ['32.09.25', 'Panguragan'],
            ['32.09.26', 'Ciwaringin'],
            ['32.09.27', 'Susukan'],
            ['32.09.28', 'Gegesik'],
            ['32.09.29', 'Kaliwedi'],
            ['32.09.30', 'Gebang'],
            ['32.09.31', 'Depok'],
            ['32.09.32', 'Pasaleman'],
            ['32.09.33', 'Pabuaran'],
            ['32.09.34', 'Karangwareng'],
            ['32.09.35', 'Tengah Tani'],
            ['32.09.36', 'Plered'],
            ['32.09.37', 'Gempol'],
            ['32.09.38', 'Greged'],
            ['32.09.39', 'Suranenggala'],
            ['32.09.40', 'Jamblang'],
        ];

        foreach ($regions as [$code, $name]) {
            Region::updateOrCreate(
                ['code' => $code],
                [
                    'parent_id' => $kabupaten->id,
                    'name' => $name,
                    'type' => 'district',
                    'level' => 1,
                    'is_active' => true,
                ]
            );
        }
    }
}   