<?php
namespace Modules\Portfolio\Database\Seeders;

use Illuminate\Database\Seeder;

class PortfolioDatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(TypeSeeder::class);
    }
}
