<?php

use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SubscriptionController::class, 'index'])->name('subscriptions.index');
Route::post('/demo/seed', [SubscriptionController::class, 'seedDemo'])->name('demo.seed');
Route::get('/subscriptions/export', [SubscriptionController::class, 'exportCsv'])->name('subscriptions.export');
Route::post('/subscriptions/import', [SubscriptionController::class, 'importCsv'])->name('subscriptions.import');
Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
Route::delete('/subscriptions/bulk', [SubscriptionController::class, 'bulkDestroy'])->name('subscriptions.bulk-destroy');
Route::put('/subscriptions/{subscription}', [SubscriptionController::class, 'update'])->name('subscriptions.update');
Route::patch('/subscriptions/{subscription}/toggle', [SubscriptionController::class, 'toggleStatus'])->name('subscriptions.toggle');
Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');
