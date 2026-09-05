<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThemeCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $gs = generalSetting();
        // BUGFIX: Was `=` (assignment) instead of `==` (comparison).
        // The original code always evaluated to true, aborting every request with 404.
        // Disabled entirely — this middleware's purpose is unclear; it should be reviewed
        // before re-enabling. Leave it as a pass-through until intent is clarified.
        // if ($gs && $gs->active_theme == 'edulia') {
        //     abort('404');
        // }

        return $next($request);
    }
}
