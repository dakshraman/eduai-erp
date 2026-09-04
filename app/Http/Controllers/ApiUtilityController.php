<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ApiUtilityController extends Controller
{
    /**
     * Return the APK secret for clients.
     * Mirrors previous closure behavior.
     */
    public function apkSecret(): JsonResponse
    {
        if (function_exists('apk_secret')) {
            try {
                return response()->json(apk_secret());
            } catch (\Throwable $e) {
                return response()->json(['error' => 'Failed to get apk_secret', 'message' => $e->getMessage()], 500);
            }
        }

        return response()->json(['error' => 'apk_secret helper not available'], 500);
    }
}
