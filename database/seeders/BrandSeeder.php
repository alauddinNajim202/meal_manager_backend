<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('brands')->insert([
            [
                'name' => 'akj',
                'slug' => 'akj',
                'status' => 'active',
            ],
            [
                'name' => 'lg',
                'slug' => 'lg',
                'status' => 'active',
            ],
            [
                'name' => 'hp',
                'slug' => 'hp',
                'status' => 'active',
            ]
        ]);
    }
}
