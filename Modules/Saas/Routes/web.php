<?php

use Illuminate\Support\Facades\Route;
use Modules\Saas\Http\Controllers\SaasSettingsController;
use Modules\Saas\Http\Controllers\SubscriptionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

// Public routes
Route::get('/pricing', [SubscriptionController::class, 'packageList'])->name('subscription.package-list');
Route::get('/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
Route::post('/checkout', [SubscriptionController::class, 'processCheckout'])->name('subscription.process');
Route::post('/stripe/webhook', [SubscriptionController::class, 'stripeWebhook'])->name('subscription.webhook');
Route::get('/thank-you', [SubscriptionController::class, 'thankYou'])->name('subscription.thank-you');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/my-subscription', [SubscriptionController::class, 'mySubscription'])->name('subscription.my');
    Route::post('/cancel-subscription', [SubscriptionController::class, 'cancelSubscription'])->name('subscription.cancel');
    Route::get('/renew-subscription', [SubscriptionController::class, 'renewSubscription'])->name('subscription.renew');
});

// Admin SaaS settings
Route::prefix('admin/saas')->middleware('auth')->group(function () {
    Route::get('/settings', [SaasSettingsController::class, 'index'])->name('saas.settings.index');
    Route::post('/settings', [SaasSettingsController::class, 'update'])->name('saas.settings.update');
});
