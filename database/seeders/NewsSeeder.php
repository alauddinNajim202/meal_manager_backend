<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Str;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
  public function run(): void
    {
        $faker = Faker::create();
        $userIds = DB::table('users')->pluck('id')->toArray();

        // Create 50 news items
        for ($i = 1; $i <= 50; $i++) {
            $newsId = DB::table('news')->insertGetId([
                'status' => $faker->randomElement(['publish', 'unpublish']),
                'slug' => Str::slug($faker->sentence(3) . '-' . $i),
                'thumbnail' => 'https://picsum.photos/seed/news' . $i . '/600/400',
                'title' => $faker->sentence(6),
                'short_description' => $faker->paragraph(2),
                'type' => $faker->word,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Each news has 2-3 details
            $detailsCount = rand(2, 3);
            for ($j = 1; $j <= $detailsCount; $j++) {
                $newsDetailId = DB::table('news_details')->insertGetId([
                    'news_id' => $newsId,
                    'title' => $faker->sentence(4),
                    'description' => $faker->paragraph(5),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Each detail has 2-4 images
                $imagesCount = rand(2, 4);
                for ($k = 1; $k <= $imagesCount; $k++) {
                    DB::table('news_details_images')->insert([
                        'news_details_id' => $newsDetailId,
                        'image' => 'https://picsum.photos/seed/news_detail_' . $newsDetailId . '_' . $k . '/800/600',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Create random likes (5-15 per news)
            $usersForLikes = $faker->randomElements($userIds, rand(5, 15));
            foreach ($usersForLikes as $uid) {
                DB::table('likes')->insertOrIgnore([
                    'user_id' => $uid,
                    'news_id' => $newsId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Create random dislikes (0-5 per news)
            $usersForDislikes = $faker->randomElements($userIds, rand(0, 5));
            foreach ($usersForDislikes as $uid) {
                DB::table('news_dislikes')->insertOrIgnore([
                    'user_id' => $uid,
                    'news_id' => $newsId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Create random comments (3-8 per news)
            for ($c = 0; $c < rand(3, 8); $c++) {
                $commentId = DB::table('comments')->insertGetId([
                    'user_id' => $faker->randomElement($userIds),
                    'news_id' => $newsId,
                    'parent_id' => null,
                    'comment' => $faker->paragraph(rand(1, 3)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Maybe add a reply (20% chance)
                if (rand(1, 100) <= 20) {
                    DB::table('comments')->insert([
                        'user_id' => $faker->randomElement($userIds),
                        'news_id' => $newsId,
                        'parent_id' => $commentId,
                        'comment' => $faker->paragraph(1),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('50 news items with details, images, likes, dislikes, and comments seeded successfully!');
    }
}
