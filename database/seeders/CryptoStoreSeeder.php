<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CryptoStore;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class CryptoStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $types = ['Tax_advisors', 'Legal_advisors', 'Crypto_partners'];

        $userIds = \App\Models\User::pluck('id')->toArray();
        if (empty($userIds)) {
            $user = \App\Models\User::create([
                'name' => 'Demo User',
                'email' => 'demo' . rand(1,100) . '@demo.com',
                'password' => bcrypt('password')
            ]);
            $userIds = [$user->id];
        }

        DB::beginTransaction();
        try {
            // Create the specific test stores first
            $specificStores = [
                [
                    'name' => 'Gareth Ratliff',
                    'title' => 'Tax Consultant',
                    'short_description' => 'Expert in Dutch Crypto Taxation.',
                    'type' => 'Tax_advisors',
                    'image' => 'https://picsum.photos/seed/tax_gareth/400/300',
                ],
                [
                    'name' => 'Jade Glenn',
                    'title' => 'Legal Compliance Officer',
                    'short_description' => 'Legal consultant for MiCA and KYC compliance.',
                    'type' => 'Legal_advisors',
                    'image' => 'https://picsum.photos/seed/legal_jade/400/300',
                ],
                [
                    'name' => 'Crypto Master',
                    'title' => 'Investment Specialist',
                    'short_description' => 'General crypto investment strategies.',
                    'type' => 'Crypto_partners',
                    'image' => 'https://picsum.photos/seed/crypto_master/400/300',
                ],
            ];
            
            foreach ($specificStores as $storeData) {
                CryptoStore::firstOrCreate(
                    ['name' => $storeData['name']],
                    array_merge($storeData, [
                        'experience_years' => 10,
                        'address' => 'Test Address',
                        'contact_email' => 'contact@' . strtolower(str_replace(' ', '', $storeData['name'])) . '.com',
                        'website' => 'https://example.com',
                        'our_mission' => '<p>Test mission</p>',
                    ])
                );
            }
            for ($i = 0; $i < 30; $i++) {
                $store = CryptoStore::create([
                    'name'              => $faker->company . ' ' . $faker->randomElement(['Advisory', 'Partners', 'Ventures', 'Solutions', 'Associates']),
                    'title'             => $faker->catchPhrase,
                    'short_description' => $faker->sentence(15),
                    'type'              => $faker->randomElement($types),
                    'image'             => 'https://picsum.photos/seed/crypto' . $i . '/400/300',
                    'experience_years'  => $faker->numberBetween(2, 15),
                    'address'           => $faker->address,
                    'contact_email'     => $faker->companyEmail,
                    'website'           => $faker->url,
                    'linkedin_url'      => 'https://linkedin.com/company/' . $faker->slug,
                    'twitter_url'       => 'https://twitter.com/' . $faker->userName,
                    'our_mission'       => '<p>' . $faker->paragraph(3) . '</p><p>' . $faker->paragraph(2) . '</p>',
                    'success_stories'   => '<p><strong>The Challenge:</strong> ' . $faker->sentence(10) . '</p><p><strong>The Solution:</strong> ' . $faker->sentence(15) . '</p>',
                    'legacy'            => 'Est. ' . $faker->year,
                    'scale'             => $faker->numberBetween(10, 200) . '+ Experts',
                ]);

                // Create random expertises (2-5 per store)
                $expertises = ['Smart Contracts', 'DeFi Compliance', 'Tokenomics', 'Tax Optimization', 'Yield Farming', 'Node Infrastructure', 'KYC/AML', 'Web3 Development'];
        
                $randomExps = $faker->randomElements($expertises, rand(2, 5));
                foreach ($randomExps as $exp) {
                    $store->expertises()->create(['name' => $exp]);
                }

                // Create random ratings for each store
                for ($j = 0; $j < rand(2, 6); $j++) {
                    \App\Models\CryptoStoreRating::create([
                        'crypto_store_id' => $store->id,
                        'user_id'         => $faker->randomElement($userIds),
                        'rating'          => rand(3, 5),
                        'comment'         => $faker->sentence(12)
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
