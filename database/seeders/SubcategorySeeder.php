<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subcategories')->insert([
            [
                'name' => 'React',
                'slug' => 'react',
                'category_id' => 1,
                'status' => 'active'
            ],
            [
                'name' => 'Vue',
                'slug' => 'vue',
                'category_id' => 1,
                'status' => 'active'
            ],
            [
                'name' => 'Laravel',
                'slug' => 'laravel',
                'category_id' => 2,
                'status' => 'active',
            ],
            [
                'name' => 'AWS',
                'slug' => 'aws',
                'category_id' => 3,
                'status' => 'active'
            ]
        ]);
    }
}
