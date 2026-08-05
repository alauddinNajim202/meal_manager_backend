<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Divisions
        $divisionsUrl = 'https://raw.githubusercontent.com/nuhil/bangladesh-geocode/master/divisions/divisions.json';
        $divisionsData = Http::get($divisionsUrl)->json();

        if (isset($divisionsData[2]['data'])) {
            $divisions = [];
            foreach ($divisionsData[2]['data'] as $division) {
                $divisions[] = [
                    'id' => $division['id'],
                    'name_en' => $division['name'],
                    'name_bn' => $division['bn_name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('divisions')->insertOrIgnore($divisions);
        }

        // 2. Districts
        $districtsUrl = 'https://raw.githubusercontent.com/nuhil/bangladesh-geocode/master/districts/districts.json';
        $districtsData = Http::get($districtsUrl)->json();

        if (isset($districtsData[2]['data'])) {
            $districts = [];
            foreach ($districtsData[2]['data'] as $district) {
                $districts[] = [
                    'id' => $district['id'],
                    'division_id' => $district['division_id'],
                    'name_en' => $district['name'],
                    'name_bn' => $district['bn_name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            // Chunk insertion if needed, but 64 rows is fine
            DB::table('districts')->insertOrIgnore($districts);
        }

        // 3. Upazilas
        $upazilasUrl = 'https://raw.githubusercontent.com/nuhil/bangladesh-geocode/master/upazilas/upazilas.json';
        $upazilasData = Http::get($upazilasUrl)->json();

        if (isset($upazilasData[2]['data'])) {
            $upazilas = [];
            foreach ($upazilasData[2]['data'] as $upazila) {
                $upazilas[] = [
                    'id' => $upazila['id'],
                    'district_id' => $upazila['district_id'],
                    'name_en' => $upazila['name'],
                    'name_bn' => $upazila['bn_name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // Insert in chunks to avoid any query length limits
            foreach (array_chunk($upazilas, 100) as $chunk) {
                DB::table('upazilas')->insertOrIgnore($chunk);
            }
        }
    }
}
