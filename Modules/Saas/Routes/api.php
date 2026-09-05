<?php

use Illuminate\Support\Facades\Route;
use Modules\Saas\Http\Controllers\SubscriptionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Stripe webhook endpoint (stateless).
|
*/

Route::post('/stripe/webhook', [SubscriptionController::class, 'stripeWebhook'])->name('api.subscription.webhook');
