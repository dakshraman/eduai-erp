<?php

use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\MaintenanceController;
use Illuminate\Support\Facades\Route;

if (config('app.app_sync')) {
    Route::get('/', 'LandingController@index')->name('/');
}

Route::group(['middleware' => ['subdomain']], function ($routes) {
    require 'tenant.php';
});

Route::get('migrate', [MaintenanceController::class, 'migrate']);

Route::post('editor/upload-file', 'UploadFileController@upload_image');
// Route::get('hide-routes',[HomeController::class,'hideRoute']);

Route::get('route-gen', 'MenuGenerateController@routeGen');

Route::get('/register', [RegistrationController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegistrationController::class, 'register'])->name('register.store');
