<?php

use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionImportExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SubscriptionController::class, 'index'])->name('subscriptions.index');
Route::post('/demo/seed', [SubscriptionController::class, 'seedDemo'])->name('demo.seed');

// CSV Import & Export routes handled by dedicated controller
Route::get('/subscriptions/export', [SubscriptionImportExportController::class, 'exportCsv'])->name('subscriptions.export');
Route::post('/subscriptions/import', [SubscriptionImportExportController::class, 'importCsv'])->name('subscriptions.import');

// Resource operations handled by SubscriptionController
Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
Route::delete('/subscriptions/bulk', [SubscriptionController::class, 'bulkDestroy'])->name('subscriptions.bulk-destroy');
Route::put('/subscriptions/{subscription}', [SubscriptionController::class, 'update'])->name('subscriptions.update');
Route::patch('/subscriptions/{subscription}/toggle', [SubscriptionController::class, 'toggleStatus'])->name('subscriptions.toggle');
Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');
