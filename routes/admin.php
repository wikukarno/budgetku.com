<?php

use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\CategoryIncomeController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\CategoryFinanceController;
use App\Http\Controllers\Admin\PaymentMethodController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

Route::prefix('/pages/admin')
    ->middleware(['auth', 'owner', '2fa-verify'])
    ->group(function () {
        Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('admin.dashboard');

        // Category Income (Inertia page, but serve JSON when AJAX for DataTables)
        Route::get('/category/income', function(){
            // If this is an Inertia visit, always return Inertia view
            if (request()->header('X-Inertia')) {
                return \Inertia\Inertia::render('Admin/CategoryIncome');
            }
            // DataTables requests include a 'draw' parameter
            if (request()->has('draw')) {
                return app(\App\Http\Controllers\Admin\CategoryIncomeController::class)->index();
            }
            // Fallback to Inertia view for normal browser requests
            return \Inertia\Inertia::render('Admin/CategoryIncome');
        })->name('admin.category.income.index');
        // Full list for client-side search/decrypt
        Route::get('/category/income/list', [CategoryIncomeController::class, 'listAll'])->name('admin.category.income.list');
        Route::get('/category/income/show', [CategoryIncomeController::class, 'show'])->name('admin.category.income.show');
        Route::post('/category/income/store', [CategoryIncomeController::class, 'store'])->name('admin.category.income.store');
        Route::delete('/category/income/delete', [CategoryIncomeController::class, 'destroy'])->name('admin.category.income.destroy');

        // Category Expense (Inertia page, but serve JSON when AJAX for DataTables)
        Route::get('/category/expense', function(){
            if (request()->header('X-Inertia')) {
                return \Inertia\Inertia::render('Admin/CategoryExpense');
            }
            if (request()->has('draw')) {
                return app(\App\Http\Controllers\Admin\CategoryFinanceController::class)->index();
            }
            return \Inertia\Inertia::render('Admin/CategoryExpense');
        })->name('admin.category.expense.index');
        Route::get('/category/expense/list', [CategoryFinanceController::class, 'listAll'])->name('admin.category.expense.list');
        Route::get('/category/expense/show', [CategoryFinanceController::class, 'show'])->name('admin.category.expense.show');
        Route::post('/category/expense/store', [CategoryFinanceController::class, 'store'])->name('admin.category.expense.store');
        Route::delete('/category/expense/delete', [CategoryFinanceController::class, 'destroy'])->name('admin.category.expense.destroy');

        // Payment Method (Inertia page, but serve JSON when AJAX for DataTables)
        Route::get('/payment-method', function(){
            if (request()->header('X-Inertia')) {
                return \Inertia\Inertia::render('Admin/PaymentMethod');
            }
            if (request()->has('draw')) {
                return app(\App\Http\Controllers\Admin\PaymentMethodController::class)->index();
            }
            return \Inertia\Inertia::render('Admin/PaymentMethod');
        })->name('admin.payment.method.index');
        Route::get('/payment-method/list', [PaymentMethodController::class, 'listAll'])->name('admin.payment.method.list');
        Route::get('/payment-method/show', [PaymentMethodController::class, 'show'])->name('admin.payment.method.show');
        Route::post('/payment-method/store', [PaymentMethodController::class, 'store'])->name('admin.payment.method.store');
        Route::delete('/payment-method/delete', [PaymentMethodController::class, 'destroy'])->name('admin.payment.method.destroy');
        // End Route custom payment method

        // Income (Inertia page, but serve JSON when AJAX for DataTables)
        Route::get('/income', function(){
            if (request()->header('X-Inertia')) {
                return \Inertia\Inertia::render('Admin/Income');
            }
            if (request()->has('draw')) {
                return app(\App\Http\Controllers\Admin\SalaryController::class)->index();
            }
            return \Inertia\Inertia::render('Admin/Income');
        })->name('admin.income.index');
        Route::get('/income/list', [SalaryController::class, 'listAll'])->name('admin.income.list');
        Route::get('/income/create', function(){ return \Inertia\Inertia::render('Admin/IncomeCreate'); })->name('admin.income.create');
        Route::get('/income/show', [SalaryController::class, 'show'])->name('admin.income.show');
        Route::get('/income/edit/{uuid}', function($uuid){ return \Inertia\Inertia::render('Admin/IncomeEdit', ['uuid' => $uuid]); })->name('admin.income.edit');
        Route::post('/income/store', [SalaryController::class, 'store'])->name('admin.income.store');
        Route::put('/income/update/{uuid}', [SalaryController::class, 'update'])->name('admin.income.update');
        Route::delete('/income/delete', [SalaryController::class, 'destroy'])->name('admin.income.destroy');
        // End Route custom income

        // Expense (Inertia page, but serve JSON when AJAX for DataTables)
        Route::get('/expense', function(){
            if (request()->header('X-Inertia')) {
                return \Inertia\Inertia::render('Admin/Expense');
            }
            if (request()->has('draw')) {
                return app(\App\Http\Controllers\Admin\FinanceController::class)->index();
            }
            return \Inertia\Inertia::render('Admin/Expense');
        })->name('admin.expense.index');
        Route::get('/expense/list', [FinanceController::class, 'listAll'])->name('admin.expense.list');
        Route::get('/expense/create', function(){ return \Inertia\Inertia::render('Admin/ExpenseCreate'); })->name('admin.expense.create');
        Route::get('/expense/edit/{uuid}', function($uuid){ return \Inertia\Inertia::render('Admin/ExpenseEdit', ['uuid' => $uuid]); })->name('admin.expense.edit');
        Route::get('/expense/show', [FinanceController::class, 'show'])->name('admin.expense.show');
        Route::post('/expense/store', [FinanceController::class, 'store'])->name('admin.expense.store');
        Route::put('/expense/update/{uuid}', [FinanceController::class, 'update'])->name('admin.expense.update');
        Route::delete('/expense/delete', [FinanceController::class, 'destroy'])->name('admin.expense.destroy');
        // End Route custom expense

        // Account (Inertia page)
        Route::get('/account', function(){
            $u = Auth::user();
            return Inertia::render('Admin/Account', [
                'user' => [
                    'uuid' => $u->uuid,
                    'name' => $u->name,
                    'email' => $u->email,
                    'email_parrent' => $u->email_parrent,
                ],
                'twoFactorEnabled' => (bool) $u->two_factor_enabled,
            ]);
        })->name('admin.account.index');
        Route::get('/account/edit/{uuid}', [AccountController::class, 'edit'])->name('admin.account.edit');
        Route::put('/account/update/{uuid}', [AccountController::class, 'update'])->name('admin.account.update');
        Route::put('/account/password/update', [AccountController::class, 'updatePassword'])->name('admin.account.password.update');
        Route::delete('/account/delete', [AccountController::class, 'destroy'])->name('admin.account.delete');
        // End Route custom account
    });
