<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MasterExpenseController;
use App\Http\Controllers\MasterIncomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/




Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    
    Route::view('/stastic', 'pages.stastic')->name('stastic');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/',[HomeController::class, 'index'])->name('home');
    Route::get('/search',[HomeController::class, 'listSearch'])->name('home-search');
    Route::get('/transactions', [HomeController::class, 'getTransactions']);

    Route::get('/idea', [HomeController::class, 'idea'])->name('idea');
    Route::post('/idea-plan', [HomeController::class, 'ideaPlan'])->name('idea_plan');
    Route::post('/money-plan', [HomeController::class, 'moneyPlan'])->name('money_plan');
    
    Route::prefix('/master-category')->group(function() {
        Route::prefix('/income')->group(function() {
            Route::get('/', [MasterIncomeController::class, 'index'])->name('mincome.index');
            Route::delete('/{id}', [MasterIncomeController::class, 'delete'])->name('mincome.delete');
            Route::post('/insert', [MasterIncomeController::class, 'insert'])->name('mincome.insert');
        });
        Route::prefix('/expense')->group(function() {
            Route::get('/', [MasterExpenseController::class, 'index'])->name('mexpense.index');
            Route::delete('/{id}', [MasterExpenseController::class, 'delete'])->name('mexpense.delete');
            Route::post('/insert', [MasterExpenseController::class, 'insert'])->name('mexpense.insert');
        });
    });
    
    Route::prefix('/transactions')->group(function() {
        //list charity
        Route::get('/list-charity', [TransactionController::class, 'listCharity'])->name('list-charity');
        Route::get('/list-goal', [TransactionController::class, 'listGoal'])->name('list-goal');
        Route::get('/add-charity', [TransactionController::class, 'addCharity'])->name('transaction.add-charity');
        Route::get('/update-charity-transaction/{id}', [TransactionController::class, 'getUpdateCharity'])->name('transaction.get-update-charity');
        Route::post('/update-charity-transaction/{id}', [TransactionController::class, 'updateCharity'])->name('transaction.update-charity');
        Route::post('/delete-charity-transaction/{id}', [TransactionController::class, 'deleteCharity'])->name('transaction.delete-charity');

        //list goal 
        Route::get('/list-goal-transaction/{id}', [TransactionController::class, 'detailGoal'])->name('transaction.list-goal');
        Route::post('/delete-goal/{id}', [TransactionController::class, 'deleteGoal'])->name('transaction.delete-goal');
        Route::post('/delete-goal-transaction/{id}', [TransactionController::class, 'deleteGoalTransaction'])->name('transaction.delete-goal-transaction');


        //get add goal transactions
        Route::get('/add-goal-transaction/{id}', [TransactionController::class, 'addGoalTransaction'])->name('add-goal-transaction');
        Route::post('/goals/{id}', [TransactionController::class, 'goalTransaction'])->name('transaction.goal');
        Route::post('/create-goals', [TransactionController::class, 'createGoal'])->name('transaction.create-goal');
        Route::get('/add-goal', [TransactionController::class, 'addGoal'])->name('transaction.add-goal');

        //get add expense and income
        Route::get('/add-expense', [TransactionController::class, 'addExpenses'])->name('transaction.add-expense');
        Route::get('/add-income', [TransactionController::class, 'addIncome'])->name('transaction.add-income');
        
        //update expense and income
        Route::get('/update-expense/{id}', [TransactionController::class, 'getUpdateExpense'])->name('transaction.update-expense');
        Route::get('/update-income/{id}', [TransactionController::class, 'getUpdateIncome'])->name('transaction.update-income');
        Route::post('/update-income/{id}', [TransactionController::class, 'updateIncome'])->name('transaction.update-income-action');
        Route::post('/update-expense/{id}', [TransactionController::class, 'updateExpenses'])->name('transaction.update-expense-action');

        //add expense and income
        Route::post('/expense', [TransactionController::class, 'expenseTransaction'])->name('transaction.expense');
        Route::post('/income', [TransactionController::class, 'incomeTransaction'])->name('transaction.income');
        Route::post('/charity', [TransactionController::class, 'charityTransaction'])->name('transaction.charity');

        //delete expense and income
        Route::post('/expense/{id}', [TransactionController::class, 'deleteExpense'])->name('transaction.delete-expense');
        Route::post('/income/{id}', [TransactionController::class, 'deleteIncome'])->name('transaction.delete-income');
    });
    Route::get('/statistic', [MasterExpenseController::class, 'index'])->name('statistic.index');
});

require __DIR__ . '/auth.php';
