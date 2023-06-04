<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\CurrencyController;
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

Route::get('/currency', [CurrencyController::class, 'index']);

Route::post('/user', [UserController::class, 'store']);

Route::middleware('auth:sanctum')->post('/account', [AccountController::class, 'store']);
Route::middleware('auth:sanctum')->get('/account', [AccountController::class, 'show']);

Route::middleware('auth:sanctum')->post('/transaction', [TransactionController::class, 'store']);
Route::middleware('auth:sanctum')->get('/transaction', [TransactionController::class, 'show']);

