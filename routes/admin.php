<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Admin\AdminController;

use App\Http\Controllers\Admin\Auth\LoginController;

use App\Http\Controllers\Admin\User\UserController;
use App\Http\Controllers\Admin\User\GradeController;
use App\Http\Controllers\Admin\User\PolicyController as UserPolicyController;
use App\Http\Controllers\Admin\User\KycVerificationController;
use App\Http\Controllers\Admin\Auth\OtpController;

use App\Http\Controllers\Admin\Coin\CoinController;

use App\Http\Controllers\Admin\Asset\AssetController;
use App\Http\Controllers\Admin\Asset\DepositController as AssetDepositController;
use App\Http\Controllers\Admin\Asset\WithdrawalController as AssetWithdrawalController;
use App\Http\Controllers\Admin\Asset\PolicyController as AssetPolicyController;

use App\Http\Controllers\Admin\Income\IncomeController;
use App\Http\Controllers\Admin\Income\DepositController as IncomeDepositController;
use App\Http\Controllers\Admin\Income\WithdrawalController as IncomeWithdrawalController;
use App\Http\Controllers\Admin\Income\PolicyController as IncomePolicyController;

use App\Http\Controllers\Admin\Trading\TradingController;
use App\Http\Controllers\Admin\Trading\PolicyController as TradingPolicyController;

use App\Http\Controllers\Admin\Marketing\MarketingController;
use App\Http\Controllers\Admin\Marketing\PolicyController as MarketingPolicyController;

use App\Http\Controllers\Admin\Mining\MiningController;
use App\Http\Controllers\Admin\Mining\PolicyController as MiningPolicyController;

use App\Http\Controllers\Admin\Board\BoardController;
use App\Http\Controllers\Admin\Board\PostController;
use App\Http\Controllers\Admin\Board\CommentController;

use App\Http\Controllers\Admin\Language\LanguageController;

use App\Http\Controllers\Admin\Manager\ManagerController;
use App\Http\Controllers\Admin\Proc\DepositToastController;



Route::get('login', [LoginController::class, 'index'])->name('admin.login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('admin.logout');

Route::middleware(['admin.auth', 'otp'])->group(function () {

    Route::get('/', [AdminController::class, 'index'])->name('admin');

    Route::get('/verify-otp', [OtpController::class, 'index'])->name('admin.otp');
    Route::post('/verify-otp/verify', [OtpController::class, 'verify'])->name('admin.otp.verify');
    Route::post('deposit-toast/{id}/read', [DepositToastController::class, 'markAsRead'])->name('deposit-toast.read');

    Route::prefix('user')->group(function () {
        Route::get('list', [UserController::class, 'list'])->name('admin.user.list');
        Route::get('view/{id}', [UserController::class, 'view'])->name('admin.user.view');
        Route::post('update', [UserController::class, 'update'])->name('admin.user.update');
        Route::post('reset', [UserController::class, 'reset'])->name('admin.user.reset');
        Route::get('export', [UserController::class, 'export'])->name('admin.user.export');
        Route::middleware(['check_admin_level:3'])->group(function () {
            Route::prefix('grade')->group(function () {
                Route::get('/', [GradeController::class, 'index'])->name('admin.user.grade');
                Route::post('store', [GradeController::class, 'store'])->name('admin.user.grade.store');
                Route::post('delete', [GradeController::class, 'delete'])->name('admin.user.grade.delete');
            });
            Route::prefix('policy')->group(function () {
                Route::post('store', [UserPolicyController::class, 'store'])->name('admin.user.policy.store');
                Route::post('update', [UserPolicyController::class, 'update'])->name('admin.user.policy.update');
                Route::get('export', [UserPolicyController::class, 'export'])->name('admin.user.policy.export');
                Route::get('{mode}', [UserPolicyController::class, 'index'])->name('admin.user.policy');
            });
        });
        Route::prefix('kyc')->group(function () {
            Route::get('list', [KycVerificationController::class, 'list'])->name('admin.user.kyc.list');
            Route::get('view/{id}', [KycVerificationController::class, 'view'])->name('admin.user.kyc.view');
            Route::post('update', [KycVerificationController::class, 'update'])->name('admin.user.kyc.update');
            Route::get('export', [KycVerificationController::class, 'export'])->name('admin.user.kyc.export');
        });
    });

    Route::prefix('coin')->group(function () {
        Route::get('/', [CoinController::class, 'index'])->name('admin.coin');
        Route::post('store', [CoinController::class, 'store'])->name('admin.coin.store');
        Route::post('update', [CoinController::class, 'update'])->name('admin.coin.update');
        Route::get('export', [CoinController::class, 'export'])->name('admin.coin.export');
    });

    Route::prefix('asset')->group(function () {
        Route::get('list', [AssetController::class, 'list'])->name('admin.asset.list');
        Route::get('view/{id}', [AssetController::class, 'view'])->name('admin.asset.view');
        Route::get('export', [AssetController::class, 'export'])->name('admin.asset.export');

        Route::prefix('deposit')->group(function () {
            Route::middleware(['check_admin_level:3'])->group(function () {
                Route::get('{id}', [AssetDepositController::class, 'deposit'])->name('admin.asset.deposit');
                Route::post('store', [AssetDepositController::class, 'store'])->name('admin.asset.deposit.store');
            });
            Route::middleware(['check_admin_level:2'])->group(function () {
                Route::post('update', [AssetDepositController::class, 'update'])->name('admin.asset.deposit.update');
            });
        });
        Route::prefix('withdrawal')->group(function () {
            Route::post('update', [AssetWithdrawalController::class, 'update'])->name('admin.asset.withdrawal.update');
        });
        Route::middleware(['check_admin_level:3'])->group(function () {
            Route::prefix('policy')->group(function () {
                Route::get('/', [AssetPolicyController::class, 'index'])->name('admin.asset.policy');
                Route::post('update', [AssetPolicyController::class, 'update'])->name('admin.asset.policy.update');
                Route::get('export', [AssetPolicyController::class, 'export'])->name('admin.asset.policy.export');
            });
        });
    });

    Route::prefix('income')->group(function () {
        Route::get('list', [IncomeController::class, 'list'])->name('admin.income.list');
        Route::get('view/{id}', [IncomeController::class, 'view'])->name('admin.income.view');
        Route::post('update', [IncomeController::class, 'update'])->name('admin.income.update');
        Route::get('export', [IncomeController::class, 'export'])->name('admin.income.export');

        Route::middleware(['check_admin_level:2'])->group(function () {
            Route::prefix('deposit')->group(function () {
                Route::post('update', [IncomeDepositController::class, 'update'])->name('admin.income.deposit.update');
            });
            Route::prefix('withdrawal')->group(function () {
                Route::post('update', [IncomeWithdrawalController::class, 'update'])->name('admin.income.withdrawal.update');
            });
        });
    });

    Route::prefix('marketing')->group(function () {
        Route::get('list', [MarketingController::class, 'list'])->name('admin.marketing.list');
        Route::get('view/{id}', [MarketingController::class, 'view'])->name('admin.marketing.view');
        Route::get('create', [MarketingController::class, 'create'])->name('admin.marketing.create');
        Route::post('store', [MarketingController::class, 'store'])->name('admin.marketing.store');
        Route::post('update', [MarketingController::class, 'update'])->name('admin.marketing.update');

        Route::middleware(['check_admin_level:3'])->group(function () {
            Route::prefix('policy')->group(function () {
                Route::get('/{id}/{mode}', [MarketingPolicyController::class, 'index'])->name('admin.marketing.policy');
                Route::post('store', [MarketingPolicyController::class, 'store'])->name('admin.marketing.policy.store');
                Route::post('update', [MarketingPolicyController::class, 'update'])->name('admin.marketing.policy.update');
            });
        });
    });

    Route::middleware(['check_admin_level:2'])->group(function () {
        Route::prefix('trading')->group(function () {
            Route::get('list', [TradingController::class, 'list'])->name('admin.trading.list');
            Route::get('export', [TradingController::class, 'export'])->name('admin.trading.export');
            Route::middleware(['check_admin_level:3'])->group(function () {
                Route::prefix('policy')->group(function () {
                    Route::get('/', [TradingPolicyController::class, 'index'])->name('admin.trading.policy');
                    Route::post('update', [TradingPolicyController::class, 'update'])->name('admin.trading.policy.update');
                    Route::get('export', [TradingPolicyController::class, 'export'])->name('admin.trading.policy.export');
                });
            });
        });

        Route::prefix('mining')->group(function () {
            Route::get('list', [MiningController::class, 'list'])->name('admin.mining.list');
            Route::get('view/{id}', [MiningController::class, 'view'])->name('admin.mining.view');
            Route::middleware(['check_admin_level:3'])->group(function () {
                Route::prefix('policy')->group(function () {
                    Route::get('/', [MiningPolicyController::class, 'index'])->name('admin.mining.policy');
                    Route::get('export', [MiningPolicyController::class, 'export'])->name('admin.mining.policy.export');
                    Route::post('check', [MiningPolicyController::class, 'check'])->name('admin.mining.policy.check');
                    Route::post('marketing-benefit-rules/{id}/get', [MiningPolicyController::class, 'getMarketingBenefitRules'])->name('admin.mining.policy.marketing-benefit-get');
                    Route::get('{mode}/{id?}', [MiningPolicyController::class, 'view'])->name('admin.mining.policy.view');
                    Route::post('store', [MiningPolicyController::class, 'store'])->name('admin.mining.policy.store');
                    Route::post('update', [MiningPolicyController::class, 'update'])->name('admin.mining.policy.update');
                });
            });
        });
    });

    Route::prefix('board')->group(function () {
        Route::middleware(['check_admin_level:3'])->group(function () {
            Route::get('list', [BoardController::class, 'list'])->name('admin.board.list');
            Route::get('view/{id}', [BoardController::class, 'view'])->name('admin.board.view');
            Route::post('update', [BoardController::class, 'update'])->name('admin.board.update');
        });
        Route::prefix('post')->group(function () {
            Route::get('/{code}', [PostController::class, 'list'])->name('admin.post.list');
            Route::get('/{code}/{mode}/{id?}', [PostController::class, 'view'])->name('admin.post.view');
            Route::post('write', [PostController::class, 'write'])->name('admin.post.write');
            Route::post('modify', [PostController::class, 'modify'])->name('admin.post.modify');
            Route::post('delete', [PostController::class, 'delete'])->name('admin.post.delete');
            Route::prefix('comment')->group(function () {
                Route::post('/', [CommentController::class, 'store'])->name('admin.post.comment');
                Route::post('update', [CommentController::class, 'update'])->name('admin.post.comment.update');
            });
        });
    });

    Route::prefix('language')->group(function () {
        Route::get('{mode}', [LanguageController::class, 'index'])->name('admin.language');
        Route::post('update', [LanguageController::class, 'update'])->name('admin.language.update');
        Route::post('delete', [LanguageController::class, 'delete'])->name('admin.language.delete');
    });

    Route::prefix('manager')->group(function () {
        Route::get('view/{id}', [ManagerController::class, 'view'])->name('admin.manager.view');
        Route::post('update', [ManagerController::class, 'update'])->name('admin.manager.update');

        Route::middleware(['check_admin_level:4'])->group(function () {
            Route::get('/', [ManagerController::class, 'list'])->name('admin.manager.list');
            Route::get('create', [ManagerController::class, 'create'])->name('admin.manager.create');
            Route::post('store', [ManagerController::class, 'store'])->name('admin.manager.store');
            Route::post('delete', [ManagerController::class, 'delete'])->name('admin.manager.delete');
            Route::get('export', [ManagerController::class, 'export'])->name('admin.manager.export');
        });
    });
});
