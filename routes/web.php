<?php

use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AdminDebtController;
use App\Http\Controllers\Admin\BillController;
use App\Http\Controllers\Admin\CategoryFinanceController;
use App\Http\Controllers\Admin\CategoryIncomeController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\PortofoliosController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TelegramBotWebHook;
use App\Http\Controllers\User\DashboardCustomerController;
use App\Http\Controllers\User\UserAccountController;
use App\Http\Controllers\User\UserCategoryFinancesController;
use App\Http\Controllers\User\UserCategoryIncomeController;
use App\Http\Controllers\User\UserFinanceController;
use App\Http\Controllers\User\UserIncomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // return abort(403, 'Forbidden');
    return view('auth.login');
})->name('keuangan');

Route::get('/auth/callback', [LoginController::class, 'handlerProviderCallback']);
Route::get('/auth/redirect', [LoginController::class, 'redirectToProvider']);


Route::prefix('/pages/admin')
    ->middleware(['auth', 'owner'])
    ->group(function () {
        Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');
        Route::post('/ubah-profile', [AccountController::class, 'ubahProfile'])->name('ubah-profile');
        Route::post('/show/finance', [FinanceController::class, 'show']);
        Route::post('/show/kategori', [CategoryFinanceController::class, 'show']);
        Route::post('/delete/kategori', [CategoryFinanceController::class, 'destroy']);
        Route::post('/delete/finance', [FinanceController::class, 'destroy']);
        Route::post('/show/salary', [SalaryController::class, 'show']);

        Route::post('/kategori/finance/show', [CategoryFinanceController::class, 'show']);
        Route::delete('/kategori/finance/delete', [CategoryFinanceController::class, 'destroy']);

        Route::post('/kategori/income/show', [CategoryIncomeController::class, 'show']);
        Route::delete('/kategori/income/delete', [CategoryIncomeController::class, 'destroy']);


        Route::delete('/bill/delete', [BillController::class, 'destroy'])->name('delete-bill');
        Route::delete('/finance/delete', [FinanceController::class, 'destroy'])->name('delete-finance');
        Route::delete('/salary/delete', [SalaryController::class, 'destroy'])->name('delete-salary');

        // Route custom debt
        // Route::get('/debt/show', [AdminDebtController::class, 'show'])->name('debt.show');
        // Route::delete('/debt/delete', [AdminDebtController::class, 'destroy'])->name('debt.delete');
        // End Route custom debt


        Route::resource('document', DocumentController::class);
        Route::resource('bill', BillController::class);
        Route::resource('salary', SalaryController::class);
        Route::resource('finance', FinanceController::class);
        // Route::resource('debt', AdminDebtController::class)->except(['show', 'destroy']);
        Route::resource('category', CategoryFinanceController::class);
        Route::resource('admin-category-income', CategoryIncomeController::class);
        Route::resource('account', AccountController::class);
    });

Route::prefix('/pages/customer')
    ->middleware(['auth', 'user'])
    ->group(function () {
        Route::get('/dashboard', [DashboardCustomerController::class, 'index'])->name('customer.dashboard');

        // Route custom category finance
        Route::get('/category-finance/show', [UserCategoryFinancesController::class, 'show'])->name('category-finance.show');
        Route::delete('/category-finance/delete', [UserCategoryFinancesController::class, 'destroy'])->name('category-finance.destroy');
        // End Route custom category finance

        // Route custom category income
        Route::get('/category-income/show', [UserCategoryIncomeController::class, 'show'])->name('category-income.show');
        Route::delete('/category-income/delete', [UserCategoryIncomeController::class, 'destroy'])->name('category-income.destroy');
        // End Route custom category income

        // Route custom income
        Route::get('/income/show', [UserIncomeController::class, 'show'])->name('income.show');
        Route::delete('/income/delete', [UserIncomeController::class, 'destroy'])->name('income.destroy');
        // End Route custom income

        // Route custom finance
        Route::get('/finance/show', [UserFinanceController::class, 'show'])->name('finance.show');
        Route::delete('/finance/delete', [UserFinanceController::class, 'destroy'])->name('finance.destroy');
        // End Route custom finance

        // Route resource category finance
        Route::resource('category-finance', UserCategoryFinancesController::class)->except(['show', 'destroy']);
        // End Route resource category finance

        // Route resource category income
        Route::resource('category-income', UserCategoryIncomeController::class)->except(['show', 'destroy']);
        // End Route resource category income


        // Route custom income
        Route::get('/income/show', [UserIncomeController::class, 'show'])->name('income.show');
        Route::delete('/income/delete', [UserIncomeController::class, 'destroy'])->name('income.destroy');
        // End Route custom income

        // Route resource income
        Route::resource('income', UserIncomeController::class)->except(['show', 'destroy']);
        // End Route resource income

        // Route custom expense
        Route::get('/expense/export', [UserFinanceController::class, 'exportExpense'])->name('expense.export');
        Route::get('/expense/show', [UserFinanceController::class, 'show'])->name('expense.show');
        Route::delete('/expense/delete', [UserFinanceController::class, 'destroy'])->name('expense.destroy');
        // End Route custom expense

        // Route resource finance
        Route::resource('expense', UserFinanceController::class)->except(['show', 'destroy']);
        // End Route resource finance

        Route::post('/ubah-profile', [UserAccountController::class, 'ubahProfile'])->name('user.ubah.profile');

        Route::resource('akun', UserAccountController::class);


    });


Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);
