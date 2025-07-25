<?php

namespace App\Providers;

use App\Repositories\CategoryFinanceRepositoryInterface;
use App\Repositories\CategoryIncomeRepositoryInterface;
use App\Repositories\EloquentCategoryFinanceRepository;
use App\Repositories\EloquentCategoryIncomeRepository;
use App\Repositories\EloquentUserRepository;
use App\Repositories\TwoFactorRepository;
use App\Repositories\UserRepositoryInterface;
use App\Services\TwoFactorService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(CategoryFinanceRepositoryInterface::class, EloquentCategoryFinanceRepository::class);
        $this->app->bind(CategoryIncomeRepositoryInterface::class, EloquentCategoryIncomeRepository::class);
        $this->app->bind(TwoFactorRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
