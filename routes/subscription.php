<?php

use App\Http\Controllers\Admin\Subscription\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->prefix('subscription')->name('subscription.')->group(function () {
    Route::get('/plans', [SubscriptionController::class, 'plans'])->name('plans');
    Route::get('/dashboard', [SubscriptionController::class, 'dashboard'])->name('dashboard');
    Route::post('/checkout', [SubscriptionController::class, 'checkout'])->name('checkout');
    Route::get('/success', [SubscriptionController::class, 'success'])->name('success');
    Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
});

Route::post('/webhook/stripe', [SubscriptionController::class, 'webhook'])->name('subscription.webhook');
