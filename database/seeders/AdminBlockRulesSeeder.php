<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminBlockRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            [
                'id' => '0213667b-8330-44af-b7a0-3d918c3fd4a9',
                'pattern' => 'insider trading',
                'reason' => 'Illegal activity',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => '024afb22-e243-4f1e-8e5b-8bdb86eef89c',
                'pattern' => 'hide crypto from tax',
                'reason' => 'Tax evasion is illegal',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => '197c996b-410e-423c-be16-0f9a74dbffe2',
                'pattern' => 'market manipulation',
                'reason' => 'Illegal activity',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => '2ff323fc-c22c-407d-b109-92e0da3220a1',
                'pattern' => 'offshore to avoid',
                'reason' => 'Potential illegal tax avoidance',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => '314d5fc8-9d84-4317-a99f-7f63e1e4e66c',
                'pattern' => 'not report crypto',
                'reason' => 'Tax fraud related',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => '330e2a09-ee49-4e41-8b39-300b8c3fb0fe',
                'pattern' => 'should i invest all',
                'reason' => 'Personal financial advice — refer to specialist',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => '375b35c4-0f7e-4a5b-8004-6b6179bc5c92',
                'pattern' => 'send crypto to receive',
                'reason' => 'Giveaway scam pattern',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => '3bfeeac1-a096-45ea-9509-7ef77a79c14f',
                'pattern' => '100% return',
                'reason' => 'Unrealistic/fraudulent claim',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => '3dd21978-59bd-475d-9279-a6b96ae5abcb',
                'pattern' => 'is it safe to invest my',
                'reason' => 'Personal financial advice — refer to specialist',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => '627883d6-8e3a-493b-9124-f05b5860de7a',
                'pattern' => 'celebrity endorsement',
                'reason' => 'Common scam tactic',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => '637126ef-f46a-422e-b79d-f49bad9f212f',
                'pattern' => 'get rich quick',
                'reason' => 'Misleading financial content',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => '63b04159-ad96-4f8a-af97-e686a4a08634',
                'pattern' => 'invest my retirement',
                'reason' => 'Personal financial advice — refer to specialist',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => '667c0e1c-2ab4-4ed3-b8e7-2b4412bdfa50',
                'pattern' => 'bomb',
                'reason' => 'Harmful content',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => '6b89030c-1c37-4103-b8cf-a8d3eace73f2',
                'pattern' => 'money laundering',
                'reason' => 'Money laundering',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => '80b7f647-48e2-4d1e-b23d-1870ebe4b989',
                'pattern' => 'hack crypto wallet',
                'reason' => 'Illegal activity',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => '8a36e06f-24ff-48a3-a692-c6eb729c519c',
                'pattern' => 'bypass kyc',
                'reason' => 'Regulatory evasion',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => '8ac49234-5ac8-4477-86eb-bbde54fddd77',
                'pattern' => 'dark web payment',
                'reason' => 'Illegal activity reference',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => '8ad36020-b6db-44f6-9fac-d2e8e8d8c82c',
                'pattern' => 'untraceable crypto',
                'reason' => 'Potential money laundering intent',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => '92ed07f9-84fd-422f-aa7b-b9677e5c8dfd',
                'pattern' => 'front running',
                'reason' => 'Illegal trading advantage',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => '9ba7f2ff-775d-4059-b1e4-ea68f1a2131d',
                'pattern' => 'buy crypto anonymously to purchase',
                'reason' => 'Potential illegal purchase',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => '9e8e3e1c-34e2-4a0e-a5f2-5ca7c1cd93c3',
                'pattern' => 'rug pull',
                'reason' => 'Fraud-related term',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => 'ab3dab91-7150-4863-b0c9-d0aaf514481d',
                'pattern' => 'how to hack exchange',
                'reason' => 'Illegal activity',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => 'b2bed04c-431c-4b5b-abe5-c56fb1dc4c47',
                'pattern' => 'double your crypto',
                'reason' => 'Classic scam pattern',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => 'b4e94213-dc23-4780-95ae-6c1d87930f40',
                'pattern' => 'fake kyc',
                'reason' => 'Identity fraud',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => 'bca0449b-25e5-484b-b2e7-cdf05c22869b',
                'pattern' => 'darknet',
                'reason' => 'Illegal marketplace reference',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => 'bfe1bb70-59e2-4cd5-bea6-7ced57b5b538',
                'pattern' => 'avoid paying tax',
                'reason' => 'Potential tax evasion intent',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => 'c987efb9-11f0-4e52-a1a7-738b0e3997f1',
                'pattern' => 'steal private key',
                'reason' => 'Illegal activity',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => 'c9d96807-8c0b-40be-84ab-07951f50d6b2',
                'pattern' => 'evade tax',
                'reason' => 'Tax evasion is illegal',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => 'cfe7b24f-1858-416c-ab57-e177b5b59426',
                'pattern' => 'kill',
                'reason' => 'Harmful content',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => 'd7241bd5-fc24-489a-a074-0e636dd6de70',
                'pattern' => 'suicide',
                'reason' => 'Sensitive — refer to appropriate support',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => 'e03e1026-1dfd-41f2-8e33-d13b07ae2a4a',
                'pattern' => 'pump and dump',
                'reason' => 'Market manipulation scheme',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => 'e37d62ad-3288-4ac6-99e4-38a53ee7470a',
                'pattern' => 'launder money',
                'reason' => 'Money laundering',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => 'ed23a772-6b75-47b3-80db-d3ef4642e3dd',
                'pattern' => 'put my savings into',
                'reason' => 'Personal financial advice — refer to specialist',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => 'f9e198cd-3c74-4458-bbc8-465857503fba',
                'pattern' => 'elon musk giveaway',
                'reason' => 'Known scam pattern',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => 'fd1175aa-625c-4e4d-86e6-aba3e4b13b0f',
                'pattern' => 'guaranteed profit',
                'reason' => 'Fraudulent promise — no investment guarantees',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
            [
                'id' => 'ffd0e8c6-d86b-44e4-a714-f43fca8fba5e',
                'pattern' => 'wash trading',
                'reason' => 'Illegal trading practice',
                'enabled' => 1,
                'created_at' => '2026-04-01 06:48:27',
            ],
        ];

        DB::table('admin_block_rules')->insert($rules);
    }
}
