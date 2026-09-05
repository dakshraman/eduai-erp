<?php

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
            \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\Localization::class,
            \App\Http\Middleware\HttpsProtocol::class,
        ]);

        $middleware->alias([
            'subdomain' => \App\Http\Middleware\SubdomainMiddleware::class,
            '2fa' => \App\Http\Middleware\TwoFactorMiddleware::class,
            'fees_due_check' => \App\Http\Middleware\FeesDueCheckMiddleware::class,
            'module' => \App\Http\Middleware\ModulePermissionMiddleware::class,
            'auth' => \App\Http\Middleware\Authenticate::class,
            'CheckUserMiddleware' => \App\Http\Middleware\CheckUserMiddleware::class,
            'CheckDashboardMiddleware' => \App\Http\Middleware\CheckDashboardMiddleware::class,
            'StudentMiddleware' => \App\Http\Middleware\StudentMiddleware::class,
            'AlumniMiddleware' => \App\Http\Middleware\AlumniMiddleware::class,
            'ParentMiddleware' => \App\Http\Middleware\ParentMiddleware::class,
            'CustomerMiddleware' => \App\Http\Middleware\CustomerMiddleware::class,
            'PM' => \App\Http\Middleware\ProductMiddleware::class,
            'cors' => \App\Http\Middleware\Cors::class,
            'XSS' => \App\Http\Middleware\XSS::class,
            'SAMiddleware' => \App\Http\Middleware\SAMiddleware::class,
            'subscriptionAccessUrl' => \App\Http\Middleware\SubscriptionAccessUrl::class,
            'userRolePermission' => \App\Http\Middleware\UserRolePermission::class,
            'json.response' => \App\Http\Middleware\ForceJsonResponse::class,
            'ThemeCheckMiddleware' => \App\Http\Middleware\ThemeCheckMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
