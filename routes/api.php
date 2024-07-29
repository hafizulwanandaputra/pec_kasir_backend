<?php

use App\Http\Middleware\jwt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(jwt::class)->group(function () {
    Route::apiResource('/detail-transaction', App\Http\Controllers\Api\DetailTransactionController::class);
    Route::get('/detail-transaction/transaction-id/{transactionid}', [App\Http\Controllers\Api\DetailTransactionController::class, 'showByTransactionId']);
    Route::get('/detail-transaction/printpdf/{transactionid}', [App\Http\Controllers\Api\TransactionController::class, 'exportPDF']);
    Route::apiResource('/outpatient', App\Http\Controllers\Api\OutpatientController::class);
    Route::apiResource('/service', App\Http\Controllers\Api\ServiceController::class);
    Route::apiResource('/transaction', App\Http\Controllers\Api\TransactionController::class);
    Route::put('/transaction/process/{process}', [App\Http\Controllers\Api\TransactionController::class, 'process']);
});
