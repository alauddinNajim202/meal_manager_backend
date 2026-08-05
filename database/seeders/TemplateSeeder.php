<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('email_templates')->insert([
            'name' => 'Default',
            'slug' => 'default',
            'subject' => 'Hey, {{name}}.',
            'body' => '{{name}} sir, Thank you for message to us. and i know your age is {{age}}.',
        ]);
    }
}
