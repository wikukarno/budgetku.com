<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserIncomeController;
use App\Http\Controllers\User\UserAccountController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\User\UserFinanceController;
use App\Http\Controllers\User\DashboardCustomerController;
use App\Http\Controllers\User\HelpCenterController;
use App\Http\Controllers\User\UserCategoryIncomeController;
use App\Http\Controllers\User\UserCategoryFinancesController;
use App\Http\Controllers\User\UserPaymentMethodController;

Route::prefix('/pages/customer')
    ->name('customer.')
    ->middleware(['auth', 'user', '2fa-verify'])
    ->group(function () {
        Route::get('/dashboard', [DashboardCustomerController::class, 'index'])->name('dashboard');

        // Route custom category finance (Inertia page, serve JSON saat AJAX DataTables)
        Route::get('/category/expense', function(){
            if (request()->header('X-Inertia')) {
                return \Inertia\Inertia::render('Customer/CategoryExpense');
            }
            if (request()->has('draw')) {
                return app(\App\Http\Controllers\User\UserCategoryFinancesController::class)->index();
            }
            return \Inertia\Inertia::render('Customer/CategoryExpense');
        })->name('category.expense.index');
        Route::get('/category/expense/list', [UserCategoryFinancesController::class, 'listAll'])->name('category.expense.list');
        Route::post('/category/expense/store', [UserCategoryFinancesController::class, 'store'])->name('category.expense.store');
        Route::get('/category/expense/show', [UserCategoryFinancesController::class, 'show'])->name('category.expense.show');
        Route::delete('/category/expense/delete', [UserCategoryFinancesController::class, 'destroy'])->name('category.expense.destroy');
        // End Route custom category finance

        // Route custom category income (Inertia page, but serve JSON when AJAX for DataTables)
        Route::get('/category/income', function(){
            // If this is an Inertia visit, return Inertia view
            if (request()->header('X-Inertia')) {
                return \Inertia\Inertia::render('Customer/CategoryIncome');
            }
            // DataTables requests include a 'draw' parameter
            if (request()->has('draw')) {
                return app(\App\Http\Controllers\User\UserCategoryIncomeController::class)->index();
            }
            // Fallback to Inertia view
            return \Inertia\Inertia::render('Customer/CategoryIncome');
        })->name('category.income.index');
        // Full list for client-side search/decrypt
        Route::get('/category/income/list', [UserCategoryIncomeController::class, 'listAll'])->name('category.income.list');
        Route::post('/category/income/store', [UserCategoryIncomeController::class, 'store'])->name('category.income.store');
        Route::get('/category/income/show', [UserCategoryIncomeController::class, 'show'])->name('category.income.show');
        Route::delete('/category/income/delete', [UserCategoryIncomeController::class, 'destroy'])->name('category.income.destroy');
        // End Route custom category income

        // Route custom income (Inertia page with JSON list)
        Route::get('/income', function(){
            if (request()->header('X-Inertia')) {
                return \Inertia\Inertia::render('Customer/Income');
            }
            if (request()->has('draw')) {
                return app(\App\Http\Controllers\User\UserIncomeController::class)->index();
            }
            return \Inertia\Inertia::render('Customer/Income');
        })->name('income.index');
        Route::get('/income/list', [UserIncomeController::class, 'listAll'])->name('income.list');
        Route::get('/income/create', function(){
            return \Inertia\Inertia::render('Customer/IncomeCreate');
        })->name('income.create');
        Route::get('/income/show', [UserIncomeController::class, 'show'])->name('income.show');
        Route::get('/income/edit/{uuid}', function($uuid){
            return \Inertia\Inertia::render('Customer/IncomeEdit', [ 'uuid' => $uuid ]);
        })->name('income.edit');
        Route::post('/income/store', [UserIncomeController::class, 'store'])->name('income.store');
        Route::put('/income/update/{uuid}', [UserIncomeController::class, 'update'])->name('income.update');
        Route::delete('/income/delete', [UserIncomeController::class, 'destroy'])->name('income.destroy');
        // End Route custom income

        // Route custom expense (Inertia page, serve JSON saat AJAX DataTables)
        Route::get('/expense', function(){
            if (request()->header('X-Inertia')) {
                return \Inertia\Inertia::render('Customer/Expense');
            }
            if (request()->has('draw')) {
                return app(\App\Http\Controllers\User\UserFinanceController::class)->index();
            }
            return \Inertia\Inertia::render('Customer/Expense');
        })->name('expense.index');
        Route::get('/expense/list', [UserFinanceController::class, 'listAll'])->name('expense.list');
        Route::get('/expense/create', function(){ return \Inertia\Inertia::render('Customer/ExpenseCreate'); })->name('expense.create');
        Route::get('/expense/edit/{uuid}', function($uuid){ return \Inertia\Inertia::render('Customer/ExpenseEdit', ['uuid'=>$uuid]); })->name('expense.edit');
        Route::get('/expense/show', [UserFinanceController::class, 'show'])->name('expense.show');
        Route::post('/expense/store', [UserFinanceController::class, 'store'])->name('expense.store');
        Route::put('/expense/update/{uuid}', [UserFinanceController::class, 'update'])->name('expense.update');
        Route::delete('/expense/delete', [UserFinanceController::class, 'destroy'])->name('expense.destroy');
        // End Route custom expense

        // Route custom expense
        Route::get('/expense/searching', [UserFinanceController::class, 'searching'])->name('expense.searching');
        Route::get('/expense/export', [UserFinanceController::class, 'exportExpense'])->name('expense.export');
        // End Route custom expense

        // Payment Method (Inertia page, JSON list for DataTables-like table)
        Route::get('/payment-method', function(){
            if (request()->header('X-Inertia')) {
                return \Inertia\Inertia::render('Customer/PaymentMethod');
            }
            return \Inertia\Inertia::render('Customer/PaymentMethod');
        })->name('payment.method.index');
        Route::get('/payment-method/list', [UserPaymentMethodController::class, 'listAll'])->name('payment.method.list');
        Route::get('/payment-method/show', [UserPaymentMethodController::class, 'show'])->name('payment.method.show');
        Route::post('/payment-method/store', [UserPaymentMethodController::class, 'store'])->name('payment.method.store');
        Route::delete('/payment-method/delete', [UserPaymentMethodController::class, 'destroy'])->name('payment.method.destroy');

        // Route help center (Inertia page)
        Route::get('/help-center', function(){
            $u = Auth::user();
            return Inertia::render('Customer/HelpCenter', [
                'user' => [ 'name' => $u->name, 'email' => $u->email ],
                'turnstileSite' => env('TURNSTILE_SITE', ''),
            ]);
        })->name('help.center.index');
        Route::post('/help-center/send', [HelpCenterController::class, 'send'])->name('help.center.send');

        // Route custom account (Inertia page)
        Route::get('/account', function(){
            $u = Auth::user();
            return Inertia::render('Customer/Account', [
                'user' => [
                    'uuid' => $u->uuid,
                    'name' => $u->name,
                    'email' => $u->email,
                    'email_parrent' => $u->email_parrent,
                ],
                'twoFactorEnabled' => (bool) $u->two_factor_enabled,
            ]);
        })->name('account.index');
        Route::get('/account/edit/{uuid}', [UserAccountController::class, 'edit'])->name('account.edit');
        Route::put('/account/update/{uuid}', [UserAccountController::class, 'update'])->name('account.update');
        Route::put('/account/password/update', [UserAccountController::class, 'updatePassword'])->name('account.password.update');
        Route::delete('/account/delete', [UserAccountController::class, 'destroy'])->name('account.delete');
        // End Route custom account

        Route::resource('akun', UserAccountController::class);
    });
