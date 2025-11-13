<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Services\BonusService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GrantRankBonus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grant:rank-bonus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant rank bonus to user incomes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::channel('bonus')->info('Starting rank bonus grant process');

        Member::chunk(100, function ($members) {
            $service = new BonusService();
            foreach ($members as $member) {
               $service->rankBonus($member);
            }
        });

        Log::channel('bonus')->info('Finished rank bonus grant process');
    }
}
