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
use App\Http\Controllers\HomeController;
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

Route::get('/', [HomeController::class, 'index'])->name('home');

// Route::get('/', function () {
//     // return abort(403, 'Forbidden');
//     return view('auth.login');
// })->name('home');

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


        Route::get('/salary', [SalaryController::class, 'index'])->name('salary.index');
        Route::get('/salary/create', [SalaryController::class, 'create'])->name('salary.create');
        Route::post('/salary/store', [SalaryController::class, 'store'])->name('salary.store');
        Route::get('/salary/edit/{id}', [SalaryController::class, 'edit'])->name('salary.edit');
        Route::put('/salary/update/{id}', [SalaryController::class, 'update'])->name('salary.update');
        Route::delete('/salary/delete/{id}', [SalaryController::class, 'destroy'])->name('salary.destroy');

        Route::resource('bill', BillController::class);
        // Route::resource('salary', SalaryController::class);
        Route::resource('finance', FinanceController::class);
        // Route::resource('debt', AdminDebtController::class)->except(['show', 'destroy']);
        Route::resource('category', CategoryFinanceController::class);
        Route::resource('admin-category-income', CategoryIncomeController::class);
        Route::resource('account', AccountController::class);
    });

    
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
            Route::delete('/account/delete', [UserAccountController::class, 'destroy'])->name('account.destroy');
            // End Route custom account
            
            Route::resource('akun', UserAccountController::class);


    });


Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);
