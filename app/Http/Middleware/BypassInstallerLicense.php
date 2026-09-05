<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BypassInstallerLicense
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->path() === 'install/license') {
            return redirect('/install/processing');
        }

        return $next($request);
    }
}
