<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Finance;
use App\Models\Salary;
use App\Policies\FinancePolicy;
use App\Policies\SalaryPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Finance::class => FinancePolicy::class,
        Salary::class => SalaryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

    }
}
