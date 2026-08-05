<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

class ClearSystemCache extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'system:clear-cache';

    /**
     * The console command description.
     */
    protected $description = 'Clear application cache and Redis cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing application cache...');
        Artisan::call('optimize:clear');
        Cache::flush();
        Redis::flushAll();
        $this->info('✅ Cache cleared successfully.');
        return Command::SUCCESS;
    }
}
