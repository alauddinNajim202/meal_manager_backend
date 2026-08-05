<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('properties')->insert([
            [
                'name' => 'red',
                'attribute_id' => 1,
                'status' => 1,
            ],
            [
                'name' => 'blue',
                'attribute_id' => 1,
                'status' => 1,
            ],
            [
                'name' => 'small',
                'attribute_id' => 2,
                'status' => 1,
            ],
            [
                'name' => 'large',
                'attribute_id' => 2,
                'status' => 1,
            ],
            [
                'name' => 'cotton',
                'attribute_id' => 3,
                'status' => 1,
            ],
            [
                'name' => 'polyester',
                'attribute_id' => 3,
                'status' => 1,
            ]
        ]);
    }
}
