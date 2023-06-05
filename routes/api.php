<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/currency', [CurrencyController::class, 'index'])->name('currency.get');

Route::post('/user', [UserController::class, 'store'])->name('user.create');

Route::middleware('auth:sanctum')->post('/account', [AccountController::class, 'store'])->name('account.create');
Route::middleware('auth:sanctum')->get('/accounts', [AccountController::class, 'index'])->name('account.index');
Route::middleware('auth:sanctum')->get('/account/{account:uuid}', [AccountController::class, 'show'])->name('account.show');

Route::middleware('auth:sanctum')->post('/transaction', [TransactionController::class, 'store'])->name('transaction.create');
Route::middleware('auth:sanctum')->get('/transactions/{account:uuid?}', [TransactionController::class, 'index'])->name('transaction.index');
Route::middleware('auth:sanctum')->get('/transaction/{transaction:reference?}', [TransactionController::class, 'show'])->name('transaction.show');
