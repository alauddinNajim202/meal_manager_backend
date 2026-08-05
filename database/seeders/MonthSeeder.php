<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $months = [
            ['name_en' => 'January', 'name_bn' => 'জানুয়ারি'],
            ['name_en' => 'February', 'name_bn' => 'ফেব্রুয়ারি'],
            ['name_en' => 'March', 'name_bn' => 'মার্চ'],
            ['name_en' => 'April', 'name_bn' => 'এপ্রিল'],
            ['name_en' => 'May', 'name_bn' => 'মে'],
            ['name_en' => 'June', 'name_bn' => 'জুন'],
            ['name_en' => 'July', 'name_bn' => 'জুলাই'],
            ['name_en' => 'August', 'name_bn' => 'আগস্ট'],
            ['name_en' => 'September', 'name_bn' => 'সেপ্টেম্বর'],
            ['name_en' => 'October', 'name_bn' => 'অক্টোবর'],
            ['name_en' => 'November', 'name_bn' => 'নভেম্বর'],
            ['name_en' => 'December', 'name_bn' => 'ডিসেম্বর'],
        ];

        DB::table('months')->insert($months);
    }
}
