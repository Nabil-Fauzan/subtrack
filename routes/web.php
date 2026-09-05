<?php

use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SubscriptionController::class, 'index'])->name('subscriptions.index');
Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
Route::patch('/subscriptions/{id}/toggle', [SubscriptionController::class, 'toggleStatus'])->name('subscriptions.toggle');
Route::delete('/subscriptions/{id}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');
