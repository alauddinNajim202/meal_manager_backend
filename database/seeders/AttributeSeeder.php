<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('attributes')->insert([
            [
                'name' => 'Color',
                'status' => 1,
            ],
            [
                'name' => 'Size',
                'status' => 1,
            ],
            [
                'name' => 'Material',
                'status' => 1,
            ]
        ]); 
    }
}
