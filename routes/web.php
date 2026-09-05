<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MaintenanceController;

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
