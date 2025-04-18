<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;

Route::post('/auth/token', [AuthController::class, 'login']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);
Route::middleware('auth:api')->get('/transactions', [TransactionController::class, 'getAllTransactions']);
Route::middleware('auth:api')->get('/transactions/{id}', [TransactionController::class, 'getById']);
Route::middleware('auth:api')->post('/transactions/{id}/cancel', [TransactionController::class, 'cancelTransaction']);

