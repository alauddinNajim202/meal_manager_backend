<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $facilities = [
            ['name_en' => 'WiFi', 'name_bn' => 'ওয়াইফাই', 'icon' => 'wifi'],
            ['name_en' => 'AC', 'name_bn' => 'এসি', 'icon' => 'ac_unit'],
            ['name_en' => 'CC Camera', 'name_bn' => 'সিসি ক্যামেরা', 'icon' => 'videocam'],
            ['name_en' => 'Security Guard', 'name_bn' => 'নিরাপত্তা কর্মী', 'icon' => 'security'],
            ['name_en' => 'Parking', 'name_bn' => 'পার্কিং', 'icon' => 'local_parking'],
            ['name_en' => 'Lift', 'name_bn' => 'লিফট', 'icon' => 'elevator'],
            ['name_en' => 'Gas', 'name_bn' => 'গ্যাস', 'icon' => 'local_fire_department'],
            ['name_en' => 'Water Heater', 'name_bn' => 'ওয়াটার হিটার', 'icon' => 'hot_tub'],
            ['name_en' => 'Generator', 'name_bn' => 'জেনারেটর', 'icon' => 'bolt'],
            ['name_en' => 'Sports/Playground', 'name_bn' => 'খেলাধুলার', 'icon' => 'sports_soccer'],
        ];

        foreach ($facilities as $facility) {
            Facility::updateOrCreate(
                ['name_en' => $facility['name_en']],
                $facility
            );
        }
    }
}
