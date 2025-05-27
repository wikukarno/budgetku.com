<?php

use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\CategoryIncomeController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\CategoryFinanceController;
use App\Http\Controllers\Admin\PaymentMethodController;
use Illuminate\Support\Facades\Route;

Route::prefix('/pages/admin')
    ->middleware(['auth', 'owner'])
    ->group(function () {
        Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('admin.dashboard');

        // Category Income
        Route::get('/category/income', [CategoryIncomeController::class, 'index'])->name('admin.category.income.index');
        Route::get('/category/income/show', [CategoryIncomeController::class, 'show'])->name('admin.category.income.show');
        Route::post('/category/income/store', [CategoryIncomeController::class, 'store'])->name('admin.category.income.store');
        Route::delete('/category/income/delete', [CategoryIncomeController::class, 'destroy'])->name('admin.category.income.destroy');

        // Category Expense
        Route::get('/category/expense', [CategoryFinanceController::class, 'index'])->name('admin.category.expense.index');
        Route::get('/category/expense/show', [CategoryFinanceController::class, 'show'])->name('admin.category.expense.show');
        Route::post('/category/expense/store', [CategoryFinanceController::class, 'store'])->name('admin.category.expense.store');
        Route::delete('/category/expense/delete', [CategoryFinanceController::class, 'destroy'])->name('admin.category.expense.destroy');

        // Payment Method
        Route::get('/payment-method', [PaymentMethodController::class, 'index'])->name('admin.payment.method.index');
        Route::get('/payment-method/show', [PaymentMethodController::class, 'show'])->name('admin.payment.method.show');
        Route::post('/payment-method/store', [PaymentMethodController::class, 'store'])->name('admin.payment.method.store');
        Route::put('/payment-method/update/{uuid}', [PaymentMethodController::class, 'update'])->name('admin.payment.method.update');
        Route::delete('/payment-method/delete', [PaymentMethodController::class, 'destroy'])->name('admin.payment.method.destroy');
        // End Route custom payment method

        // Income
        Route::get('/income', [SalaryController::class, 'index'])->name('admin.income.index');
        Route::get('/income/create', [SalaryController::class, 'create'])->name('admin.income.create');
        Route::get('/income/show', [SalaryController::class, 'show'])->name('admin.income.show');
        Route::get('/income/edit/{uuid}', [SalaryController::class, 'edit'])->name('admin.income.edit');
        Route::post('/income/store', [SalaryController::class, 'store'])->name('admin.income.store');
        Route::put('/income/update/{uuid}', [SalaryController::class, 'update'])->name('admin.income.update');
        Route::delete('/income/delete', [SalaryController::class, 'destroy'])->name('admin.income.destroy');
        // End Route custom income

        // Expense
        Route::get('/expense', [FinanceController::class, 'index'])->name('admin.expense.index');
        Route::get('/expense/create', [FinanceController::class, 'create'])->name('admin.expense.create');
        Route::get('/expense/edit/{uuid}', [FinanceController::class, 'edit'])->name('admin.expense.edit');
        Route::get('/expense/show', [FinanceController::class, 'show'])->name('admin.expense.show');
        Route::post('/expense/store', [FinanceController::class, 'store'])->name('admin.expense.store');
        Route::put('/expense/update/{uuid}', [FinanceController::class, 'update'])->name('admin.expense.update');
        Route::delete('/expense/delete', [FinanceController::class, 'destroy'])->name('admin.expense.destroy');
        // End Route custom expense

        // Account
        Route::get('/account', [AccountController::class, 'index'])->name('admin.account.index');
        Route::get('/account/edit/{uuid}', [AccountController::class, 'edit'])->name('admin.account.edit');
        Route::put('/account/update/{uuid}', [AccountController::class, 'update'])->name('admin.account.update');
        Route::put('/account/password/update', [AccountController::class, 'updatePassword'])->name('admin.account.password.update');
        Route::delete('/account/delete', [AccountController::class, 'destroy'])->name('admin.account.delete');
        // End Route custom account
    });