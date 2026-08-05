<?php

namespace Modules\Portfolio\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('types')->insert([
            [
                'name' => 'academic',
                'slug' => 'academic',
                'status' => 'active',
            ],
            [
                'name' => 'company',
                'slug' => 'company',
                'status' => 'active',
            ],
            [
                'name' => 'personal',
                'slug' => 'personal',
                'status' => 'active',
            ]
        ]);
    }
    
}
