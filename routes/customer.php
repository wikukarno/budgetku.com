<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserIncomeController;
use App\Http\Controllers\User\UserAccountController;
use App\Http\Controllers\User\UserFinanceController;
use App\Http\Controllers\User\DashboardCustomerController;
use App\Http\Controllers\User\UserCategoryIncomeController;
use App\Http\Controllers\User\UserCategoryFinancesController;

Route::prefix('/pages/customer')
    ->name('customer.')
    ->middleware(['auth', 'user'])
    ->group(function () {
        Route::get('/dashboard', [DashboardCustomerController::class, 'index'])->name('dashboard');

        // Route custom category finance
        Route::get('/category/expense', [UserCategoryFinancesController::class, 'index'])->name('category.expense.index');
        Route::post('/category/expense/store', [UserCategoryFinancesController::class, 'store'])->name('category.expense.store');
        Route::get('/category/expense/show', [UserCategoryFinancesController::class, 'show'])->name('category.expense.show');
        Route::delete('/category/expense/delete', [UserCategoryFinancesController::class, 'destroy'])->name('category.expense.destroy');
        // End Route custom category finance

        // Route custom category income
        Route::get('/category/income', [UserCategoryIncomeController::class, 'index'])->name('category.income.index');
        Route::post('/category/income/store', [UserCategoryIncomeController::class, 'store'])->name('category.income.store');
        Route::get('/category/income/show', [UserCategoryIncomeController::class, 'show'])->name('category.income.show');
        Route::delete('/category/income/delete', [UserCategoryIncomeController::class, 'destroy'])->name('category.income.destroy');
        // End Route custom category income

        // Route custom income
        Route::get('/income', [UserIncomeController::class, 'index'])->name('income.index');
        Route::get('/income/create', [UserIncomeController::class, 'create'])->name('income.create');
        Route::get('/income/show', [UserIncomeController::class, 'show'])->name('income.show');
        Route::get('/income/edit/{id}', [UserIncomeController::class, 'edit'])->name('income.edit');
        Route::post('/income/store', [UserIncomeController::class, 'store'])->name('income.store');
        Route::put('/income/update/{id}', [UserIncomeController::class, 'update'])->name('income.update');
        Route::delete('/income/delete', [UserIncomeController::class, 'destroy'])->name('income.destroy');
        // End Route custom income

        // Route custom expense
        Route::get('/expense', [UserFinanceController::class, 'index'])->name('expense.index');
        Route::get('/expense/create', [UserFinanceController::class, 'create'])->name('expense.create');
        Route::get('/expense/edit/{id}', [UserFinanceController::class, 'edit'])->name('expense.edit');
        Route::get('/expense/show', [UserFinanceController::class, 'show'])->name('expense.show');
        Route::post('/expense/store', [UserFinanceController::class, 'store'])->name('expense.store');
        Route::put('/expense/update/{id}', [UserFinanceController::class, 'update'])->name('expense.update');
        Route::delete('/expense/delete', [UserFinanceController::class, 'destroy'])->name('expense.destroy');
        // End Route custom expense

        // Route custom expense
        Route::get('/expense/searching', [UserFinanceController::class, 'searching'])->name('expense.searching');
        Route::get('/expense/export', [UserFinanceController::class, 'exportExpense'])->name('expense.export');
        // End Route custom expense

        // Route custom account
        Route::get('/account', [UserAccountController::class, 'index'])->name('account.index');
        Route::get('/account/edit/{id}', [UserAccountController::class, 'edit'])->name('account.edit');
        Route::put('/account/update/{id}', [UserAccountController::class, 'update'])->name('account.update');
        Route::put('/account/password/update', [UserAccountController::class, 'updatePassword'])->name('account.password.update');
        Route::delete('/account/delete', [UserAccountController::class, 'destroy'])->name('account.delete');
        // End Route custom account

        Route::resource('akun', UserAccountController::class);
    });