<?php

namespace App\Console\Commands;

use App\Models\Mining;
use App\Models\MiningReward;
use Illuminate\Console\Command;

class GenerateMiningReward extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:mining-reward';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Distribute daily mining rewards to users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Mining::storeMiningReward();
        MiningReward::distributeProfit();
        Mining::finalizePayout();
    }
}
