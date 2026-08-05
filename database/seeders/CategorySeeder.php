<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name' => 'ফ্যামিলি বাসা',
                'slug' => 'family-house',
                'status' => 'active',
            ],
            [
                'name' => 'ব্যাচেলর',
                'slug' => 'bachelor',
                'status' => 'active',
            ],
            [
                'name' => 'অফিস/ক্লিনিক',
                'slug' => 'office-clinic',
                'status' => 'active',
            ],
            [
                'name' => 'মেস',
                'slug' => 'mess',
                'status' => 'active',
            ]
        ]);
    }
}
