<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

use App\Http\Controllers\HomeController;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\FindAccountController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\OtpController;

use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\DashboardController;
use App\Http\Controllers\Profile\KycVerificationController;

use App\Http\Controllers\Asset\AssetController;
use App\Http\Controllers\Asset\DepositController as AssetDepositController;
use App\Http\Controllers\Asset\WithdrawalController as AssetWithdrawalController;

use App\Http\Controllers\Income\IncomeController;
use App\Http\Controllers\Income\DepositController as IncomeDepositController;
use App\Http\Controllers\Income\WithdrawalController as IncomeWithdrawalController;

use App\Http\Controllers\Trading\TradingController;

use App\Http\Controllers\Mining\MiningController;

use App\Http\Controllers\Chart\RefChartController;
use App\Http\Controllers\Chart\AffChartController;

use App\Http\Controllers\Board\BoardController;
use App\Http\Controllers\Board\PostController;
use App\Http\Controllers\Board\CommentController;

use App\Http\Controllers\About\AboutController;

use App\Http\Controllers\Proc\LanguageController;
use App\Http\Controllers\Proc\PopupController;
use App\Http\Controllers\Proc\FileUploadController;

Route::get('test', [TestController::class, 'index'])->name('test');

Route::get('register/terms', [RegisterController::class, 'terms'])->name('register.terms');
Route::get('register/{mid?}', [RegisterController::class, 'index'])->name('register');
Route::post('register', [RegisterController::class, 'register']);
Route::post('register/account-check', [RegisterController::class, 'accountCheck'])->name('register.accountCheck');
Route::post('register/email-check', [RegisterController::class, 'emailCheck'])->name('register.emailCheck');
Route::post('register/parent-check', [RegisterController::class, 'parentCheck'])->name('register.parentCheck');

Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::post('verify-code/request', [VerificationController::class, 'sendCode'])->name('verify.code.send');
Route::post('verify-code/check', [VerificationController::class, 'checkCode'])->name('verify.code.check');
Route::get('account/request', [FindAccountController::class, 'index'])->name('account.request');
Route::get('account/result', [FindAccountController::class, 'result'])->name('account.result');
Route::get('password/request', [ForgotPasswordController::class, 'index'])->name('password.request');
Route::get('password/reset', [ResetPasswordController::class, 'index'])->name('password.reset');
Route::post('password/update', [ResetPasswordController::class, 'update'])->name('password.update');

Route::prefix('chart')->group(function () {
    Route::get('ref', [RefChartController::class, 'index'])->name('chart.ref');
    Route::get('aff', [AffChartController::class, 'index'])->name('chart.aff');
});

Route::middleware(['auth', 'session.timeout'])->group(function () {


    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/home', function () {
        return redirect()->route('home');
    });

    Route::prefix('verify-otp')->group(function () {
        Route::get('/', [OtpController::class, 'index'])->name('otp');
        Route::post('verify', [OtpController::class, 'verify'])->name('otp.verify');
        Route::prefix('email')->group(function () {
            Route::get('/', [OtpController::class, 'email'])->name('otp.email');
            Route::get('verify', [OtpController::class, 'email_verify'])->name('otp.email.verify');
        });
    });

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile');
        Route::post('update', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/password', [ProfileController::class, 'password'])->name('profile.password');
        Route::post('/password/update', [ProfileController::class, 'passwordUpdate'])->name('profile.password.update');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('profile.dashboard');

        Route::prefix('kyc')->group(function () {
            Route::get('/', [KycVerificationController::class, 'index'])->name('kyc');
            Route::post('store', [KycVerificationController::class, 'store'])->name('kyc.store');
        });
    });

    Route::prefix('asset')->group(function () {
        Route::get('/', [AssetController::class, 'index'])->name('asset');
        Route::prefix('list')->group(function () {
            Route::get('/', [AssetController::class, 'list'])->name('asset.list');
            Route::post('load-more', [AssetController::class, 'loadMore'])->name('asset.list.loadMore');
        });

        Route::prefix('deposit')->group(function () {
            Route::get('/', [AssetDepositController::class, 'index'])->name('asset.deposit');
            Route::post('confirm', [AssetDepositController::class, 'confirm'])->name('asset.deposit.confirm');
            Route::post('store', [AssetDepositController::class, 'store'])->name('asset.deposit.store');
            Route::prefix('list')->group(function () {
                Route::get('/', [AssetDepositController::class, 'list'])->name('asset.deposit.list');
                Route::post('load-more', [AssetDepositController::class, 'loadMore'])->name('asset.deposit.list.loadMore');
            });
        });

        Route::prefix('withdrawal')->group(function () {
            Route::middleware(['otp'])->group(function () {
                Route::get('/', [AssetWithdrawalController::class, 'index'])->name('asset.withdrawal');
            });
            Route::post('store', [AssetWithdrawalController::class, 'store'])->name('asset.withdrawal.store');
            Route::get('complete/{id}', [AssetWithdrawalController::class, 'complete'])->name('asset.withdrawal.complete');
            Route::prefix('list')->group(function () {
                Route::get('/', [AssetWithdrawalController::class, 'list'])->name('asset.withdrawal.list');
                Route::post('load-more', [AssetWithdrawalController::class, 'loadMore'])->name('asset.withdrawal.list.loadMore');
            });
        });
    });

    Route::prefix('income')->group(function () {
        Route::get('/', [IncomeController::class, 'index'])->name('income');
        Route::prefix('list')->group(function () {
            Route::get('{id}', [IncomeController::class, 'list'])->name('income.list');
            Route::post('load-more', [IncomeController::class, 'loadMore'])->name('income.list.loadMore');
        });
        Route::prefix('deposit')->group(function () {
            Route::get('/', [IncomeDepositController::class, 'index'])->name('income.deposit');
            Route::post('store', [IncomeDepositController::class, 'store'])->name('income.deposit.store');
            Route::prefix('list')->group(function () {
                Route::get('/', [IncomeDepositController::class, 'list'])->name('income.deposit.list');
                Route::post('load-more', [IncomeDepositController::class, 'loadMore'])->name('income.deposit.list.loadMore');
            });
        });
        Route::prefix('withdrawal')->group(function () {
            //Route::middleware(['otp'])->group(function () {
                Route::get('/', [IncomeWithdrawalController::class, 'index'])->name('income.withdrawal');
            //});
            Route::post('store', [IncomeWithdrawalController::class, 'store'])->name('income.withdrawal.store');
            Route::get('complete/{id}', [IncomeWithdrawalController::class, 'complete'])->name('income.withdrawal.complete');
            Route::prefix('list')->group(function () {
                Route::get('/', [IncomeWithdrawalController::class, 'list'])->name('income.withdrawal.list');
                Route::post('load-more', [IncomeWithdrawalController::class, 'loadMore'])->name('income.withdrawal.list.loadMore');
            });
        });
    });

    Route::prefix('trading')->group(function () {
        Route::get('/', [TradingController::class, 'index'])->name('trading');
        Route::get('wait', [TradingController::class, 'wait'])->name('trading.wait');
        Route::post('store', [TradingController::class, 'store'])->name('trading.store');
        Route::get('done', [TradingController::class, 'done'])->name('trading.done');
        Route::prefix('list')->group(function () {
            Route::get('/', [TradingController::class, 'list'])->name('trading.list');
            Route::post('load-more', [TradingController::class, 'loadMore'])->name('trading.list.loadMore');
        });
    });

    Route::prefix('mining')->group(function () {
        Route::post('data', [MiningController::class, 'data'])->name('mining.data');
        Route::get('confirm/{id}', [MiningController::class, 'confirm'])->name('mining.confirm');
        Route::post('store', [MiningController::class, 'store'])->name('mining.store');
        Route::get('list', [MiningController::class, 'list'])->name('mining.list');

        Route::get('{id}', [MiningController::class, 'index'])->name('mining');
        /*
        Route::get('detail', [MiningController::class, 'detail'])->name('mining.detail');
        Route::get('profit/{id}', [MiningController::class, 'profit'])->name('mining.profit');
        */
    });

    Route::prefix('board')->group(function () {
        Route::get('/{code}', [BoardController::class, 'list'])->name('board.list');
        Route::get('/{code}/{mode}/{id?}', [PostController::class, 'view'])->name('board.view');
        Route::post('/write', [PostController::class, 'write'])->name('board.write');
        Route::post('/modify', [PostController::class, 'modify'])->name('board.modify');
        Route::post('/delete/{code}/{id}', [PostController::class, 'delete'])->name('board.delete');
        Route::post('/comment', [CommentController::class, 'store'])->name('board.comment');
    });

    Route::get('about', [AboutController::class, 'index'])->name('about');
});

Route::get('/change-language/{locale}', [LanguageController::class, 'changeLanguage'])->name('change.language');
Route::post('/popup/hide', [PopupController::class, 'hide'])->name('popup.hide');
Route::post('/file/presigned-url', [FileUploadController::class, 'generatePresignedUrl'])->name('file.presigned-url');
