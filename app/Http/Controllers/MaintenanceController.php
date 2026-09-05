<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;

class MaintenanceController extends Controller
{
    /**
     * Run pending migrations.
     * Preserves original behavior from the route closure: only allow user id 1.
     */
    public function runMigrate(Request $request): RedirectResponse
    {
        // Keep the same guard as the original closure to avoid changing behavior.
        if (!Auth::check() || Auth::id() !== 1) {
            abort(404);
        }

        try {
            Artisan::call('migrate', ['--force' => true]);

            // Use the Toastr facade (alias exists in config) to preserve UX behavior
            if (class_exists('\Brian2694\\Toastr\\Facades\\Toastr')) {
                \Brian2694\Toastr\Facades\Toastr::success('Migration run successfully');
            }

            return redirect()->to(url('/admin-dashboard'));
        } catch (\Throwable $e) {
            Log::error('Migration via web route failed: ' . $e->getMessage(), ['exception' => $e]);

            // Keep user-friendly redirect as original closure did on success; show error via session
            return redirect()->back()->with('error', 'Migration failed: ' . $e->getMessage());
        }
    }

    // Backwards-compatible alias in case routes use 'migrate' as method name.
    public function migrate(Request $request): RedirectResponse
    {
        return $this->runMigrate($request);
    }
}
