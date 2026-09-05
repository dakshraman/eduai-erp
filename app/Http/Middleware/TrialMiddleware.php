<?php

namespace App\Http\Middleware;

use App\Models\SmSchool;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrialMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        if ($user->is_saas && $user->school_id) {
            $school = SmSchool::find($user->school_id);

            if ($school) {
                $hasActiveSubscription = $school->package_id && $school->ending_date && now()->lte($school->ending_date);

                $isInTrial = $school->created_at && now()->lte($school->created_at->addDays(14));

                if (! $hasActiveSubscription && ! $isInTrial) {
                    return redirect()->route('subscription.plans');
                }
            }
        }

        return $next($request);
    }
}
