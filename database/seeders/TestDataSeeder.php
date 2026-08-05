<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\CryptoStore;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Admin Block Rules (5 rules) ──────────────────────────
        DB::table('admin_block_rules')->truncate();
        DB::table('admin_block_rules')->insert([
            ['id' => Str::uuid(), 'pattern' => 'pump and dump',        'reason' => 'Market manipulation scheme',  'enabled' => 1, 'created_at' => now()],
            ['id' => Str::uuid(), 'pattern' => 'hide crypto from tax', 'reason' => 'Tax evasion is illegal',       'enabled' => 1, 'created_at' => now()],
            ['id' => Str::uuid(), 'pattern' => 'hack crypto wallet',   'reason' => 'Illegal activity',             'enabled' => 1, 'created_at' => now()],
            ['id' => Str::uuid(), 'pattern' => 'money laundering',     'reason' => 'Money laundering',             'enabled' => 1, 'created_at' => now()],
            ['id' => Str::uuid(), 'pattern' => 'bypass kyc',           'reason' => 'Regulatory evasion',           'enabled' => 1, 'created_at' => now()],
        ]);

        // ── 2. Category Overrides (5 rules) ─────────────────────────
        DB::table('admin_category_overrides')->truncate();
        DB::table('admin_category_overrides')->insert([
            ['id' => Str::uuid(), 'question_pattern' => 'bitcoin tax',      'forced_category' => 'tax',     'enabled' => 1, 'created_at' => now()],
            ['id' => Str::uuid(), 'question_pattern' => 'crypto belasting',  'forced_category' => 'tax',     'enabled' => 1, 'created_at' => now()],
            ['id' => Str::uuid(), 'question_pattern' => 'mica regulation',   'forced_category' => 'legal',   'enabled' => 1, 'created_at' => now()],
            ['id' => Str::uuid(), 'question_pattern' => 'crypto license',    'forced_category' => 'legal',   'enabled' => 1, 'created_at' => now()],
            ['id' => Str::uuid(), 'question_pattern' => 'what is bitcoin',   'forced_category' => 'general', 'enabled' => 1, 'created_at' => now()],
        ]);

        // ── 3. Crypto Stores (4 stores) ─────────────────────────────
        CryptoStore::query()->delete();

        CryptoStore::create([
            'name'              => 'Gareth Ratliff',
            'title'             => 'Tax Consultant',
            'short_description' => 'Expert in Dutch crypto taxation and Box 3 reporting.',
            'type'              => 'Tax_advisors',
            'experience_years'  => 12,
            'contact_email'     => 'gareth@taxadvisor.nl',
            'website'           => 'https://example.com/gareth',
            'our_mission'       => '<p>Helping crypto investors stay tax compliant in the Netherlands.</p>',
        ]);

        CryptoStore::create([
            'name'              => 'Jade Glenn',
            'title'             => 'Legal Compliance Officer',
            'short_description' => 'Specialist in MiCA regulation, KYC/AML and crypto licensing.',
            'type'              => 'Legal_advisors',
            'experience_years'  => 9,
            'contact_email'     => 'jade@legalcrypto.nl',
            'website'           => 'https://example.com/jade',
            'our_mission'       => '<p>Helping crypto businesses stay compliant with EU regulations.</p>',
        ]);

        CryptoStore::create([
            'name'              => 'Crypto Master',
            'title'             => 'Investment Specialist',
            'short_description' => 'General crypto investment strategies and portfolio management.',
            'type'              => 'Crypto_partners',
            'experience_years'  => 7,
            'contact_email'     => 'info@cryptomaster.nl',
            'website'           => 'https://example.com/cryptomaster',
            'our_mission'       => '<p>Empowering crypto investors with smart strategies.</p>',
        ]);

        CryptoStore::create([
            'name'              => 'DeFi Expert',
            'title'             => 'DeFi & Web3 Specialist',
            'short_description' => 'Expertise in DeFi protocols, yield farming and smart contracts.',
            'type'              => 'Crypto_partners',
            'experience_years'  => 5,
            'contact_email'     => 'info@defiexpert.nl',
            'website'           => 'https://example.com/defiexpert',
            'our_mission'       => '<p>Guiding users through the world of decentralized finance.</p>',
        ]);

        $this->command->info('✅ Block Rules: '       . DB::table('admin_block_rules')->count());
        $this->command->info('✅ Category Overrides: ' . DB::table('admin_category_overrides')->count());
        $this->command->info('✅ Crypto Stores: '      . CryptoStore::count());
        $this->command->newLine();

        CryptoStore::all(['id', 'name', 'slug', 'type'])->each(function ($s) {
            $this->command->line("  → [{$s->type}] {$s->name}  (slug: {$s->slug})");
        });
    }
}
