<?php

use App\Http\Middleware\AlumniMiddleware;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckDashboardMiddleware;
use App\Http\Middleware\CheckMaintenanceMode;
use App\Http\Middleware\CheckUserMiddleware;
use App\Http\Middleware\Cors;
use App\Http\Middleware\CustomerMiddleware;
use App\Http\Middleware\FeesDueCheckMiddleware;
use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\HttpsProtocol;
use App\Http\Middleware\Localization;
use App\Http\Middleware\ModulePermissionMiddleware;
use App\Http\Middleware\ParentMiddleware;
use App\Http\Middleware\ProductMiddleware;
use App\Http\Middleware\SAMiddleware;
use App\Http\Middleware\StudentMiddleware;
use App\Http\Middleware\SubdomainMiddleware;
use App\Http\Middleware\SubscriptionAccessUrl;
use App\Http\Middleware\ThemeCheckMiddleware;
use App\Http\Middleware\TrialMiddleware;
use App\Http\Middleware\TwoFactorMiddleware;
use App\Http\Middleware\UserRolePermission;
use App\Http\Middleware\XSS;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend([
            CheckMaintenanceMode::class,
        ]);

        $middleware->web(append: [
            Localization::class,
            HttpsProtocol::class,
        ]);

        $middleware->alias([
            'subdomain' => SubdomainMiddleware::class,
            '2fa' => TwoFactorMiddleware::class,
            'fees_due_check' => FeesDueCheckMiddleware::class,
            'module' => ModulePermissionMiddleware::class,
            'auth' => Authenticate::class,
            'CheckUserMiddleware' => CheckUserMiddleware::class,
            'CheckDashboardMiddleware' => CheckDashboardMiddleware::class,
            'StudentMiddleware' => StudentMiddleware::class,
            'AlumniMiddleware' => AlumniMiddleware::class,
            'ParentMiddleware' => ParentMiddleware::class,
            'CustomerMiddleware' => CustomerMiddleware::class,
            'PM' => ProductMiddleware::class,
            'cors' => Cors::class,
            'XSS' => XSS::class,
            'trial' => TrialMiddleware::class,
            'SAMiddleware' => SAMiddleware::class,
            'subscriptionAccessUrl' => SubscriptionAccessUrl::class,
            'userRolePermission' => UserRolePermission::class,
            'json.response' => ForceJsonResponse::class,
            'ThemeCheckMiddleware' => ThemeCheckMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
