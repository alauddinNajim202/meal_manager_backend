<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Str;

class RollbackNewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all news records
        $newsItems = DB::table('news')->get();
        DB::table('likes')->delete();
        DB::table('news_dislikes')->delete();
        DB::table('comments')->delete();


        foreach ($newsItems as $news) {

            DB::table('news')
                ->where('id', $news->id)
                ->update([
                    'thumbnail' => 'uploads/news/photo_2026-01-28_18-23-14.jpg'
                ]);
            $users = DB::table('users')->get();


            $users->random(rand(1, $users->count()))->each(function ($user) use ($news) {
                DB::table('likes')->insert([
                    'user_id'    => $user->id,
                    'news_id'    => $news->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });


            $users->random(rand(1, $users->count()))->each(function ($user) use ($news) {
                DB::table('news_dislikes')->insert([
                    'user_id'    => $user->id,
                    'news_id'    => $news->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            $users->each(function ($user) use ($news) {
                DB::table('comments')->insert([
                    'user_id'    => $user->id,
                    'news_id'    => $news->id,
                    'comment'    => fake()->paragraph(rand(1, 3)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });


            // $newsDetails = DB::table('news_details')
            //     ->where('news_id', $news->id)
            //     ->get();

            // foreach ($newsDetails as $detail) {
            //     DB::table('news_details_images')
            //         ->insert([
            //             'image' => 'uploads/news/photo_2026-01-28_18-23-14.jpg',
            //             'news_details_id' => $detail->id,
            //             'created_at' => now(),
            //             'updated_at' => now()
            //         ]);
            // }
        }
    }
}
