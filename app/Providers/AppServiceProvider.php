<?php

namespace App\Providers;


use App\Models\GradePolicy;
use App\Models\AssetPolicy;
use App\Models\ReferralPolicy;
use App\Models\ReferralMatchingPolicy;
use App\Models\RankPolicy;
use App\Models\LevelPolicy;
use App\Models\LevelConditionPolicy;
use App\Models\MiningPolicy;
use App\Models\LanguagePolicy;
use App\Models\DepositToast;
use App\Observers\PolicyObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;


class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        GradePolicy::observe(PolicyObserver::class);
        AssetPolicy::observe(PolicyObserver::class);
        ReferralPolicy::observe(PolicyObserver::class);
        ReferralMatchingPolicy::observe(PolicyObserver::class);
        RankPolicy::observe(PolicyObserver::class);
        LevelPolicy::observe(PolicyObserver::class);
        LevelConditionPolicy::observe(PolicyObserver::class);
        MiningPolicy::observe(PolicyObserver::class);

        View::composer('*', function ($view) {
            $languages = LanguagePolicy::where('type', 'locale')->first()->content ?? [];

            $view->with('locales', $languages);
        });

        View::composer('admin.layouts.master', function ($view) {
            $admin = auth()->guard('admin')->user();

            if ($admin && $admin->admin_level > 1) {
                $toasts = DepositToast::where('is_read', false)->latest()->get();
                $view->with('toasts', $toasts);
            } else {
                $view->with('toasts', collect());
            }
        });
    }
}
