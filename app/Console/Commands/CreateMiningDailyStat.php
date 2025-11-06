<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MiningPolicy;

class CreateMiningDailyStat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:mining-daily-stat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $policies = MiningPolicy::all();

        foreach ($policies as $policy) {
            $policy->setDailyStat();
        }
    }
}
