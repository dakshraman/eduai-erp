<?php

use App\Support\ModuleRegistry;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| FIXED: The original file included admin_tenant.php TWICE:
|   1. Inside the Saas module conditional block
|   2. Again unconditionally in the second Route::group
|
| This caused admin_tenant.php (2,362 routes) to be registered twice on
| every request, doubling route compilation time and memory usage.
|
| Fix: Only include once. The subdomain middleware handles SaaS routing.
*/

Route::group(['middleware' => ['subdomain']], function () {
    require __DIR__ . '/admin_tenant.php';
});

//Route::get('migration-run', function () {
//    Artisan::call('migrate', ['--force' => true]);
//});