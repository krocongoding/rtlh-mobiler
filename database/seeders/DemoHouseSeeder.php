<?php

namespace Database\Seeders;

use App\Models\House;
use Illuminate\Database\Seeder;

class DemoHouseSeeder extends Seeder
{
    public function run(): void
    {
        $regionId = 1;

        for ($i = 1; $i <= 500; $i++) {
            House::create([
                'region_id' => $regionId,
                'house_code' => 'DEMO-RTLH-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'address' => 'Alamat Demo RTLH ' . $i . ', Kabupaten Cirebon',
                'block' => 'BLOK DEMO ' . (($i % 10) + 1),
                'rt' => str_pad(($i % 15) + 1, 2, '0', STR_PAD_LEFT),
                'rw' => str_pad(($i % 8) + 1, 2, '0', STR_PAD_LEFT),
                'area_m2' => rand(30, 120),
                'occupant_count' => rand(1, 7),
                'household_count' => 1,
                'survey_year' => 2026,
                'status' => 'draft',
                'is_public' => false,
            ]);
        }

        $this->command->info('500 data DEMO RTLH berhasil dibuat.');
    }
}   