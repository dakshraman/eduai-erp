<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Log;
use Laravel\Octane\RoadRunner\ServerProcessInspector as RoadRunnerServerProcessInspector;

trait RestartOctan
{
    /**
     * Reload Octane workers
     */
    protected function reloadOctane(): bool
    {

        try {
            $inspector = app(RoadRunnerServerProcessInspector::class);
            if (! $inspector->serverIsRunning()) {
                return false;
            }
            $inspector->reloadServer();

            return true;

        } catch (Exception $e) {
            Log::error('Failed to reload Octane: '.$e->getMessage());

            return false;
        }
    }
}
