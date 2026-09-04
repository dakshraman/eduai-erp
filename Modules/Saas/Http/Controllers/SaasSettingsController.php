<?php

namespace Modules\Saas\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Saas\Entities\SaasSettings;

class SaasSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show SaaS settings page.
     */
    public function index()
    {
        try {
            $settings = SaasSettings::all()->keyBy('key');

            return view('saas::settings.saasSettings', compact('settings'));
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable|string',
            'settings.*.type' => 'required|in:text,boolean,json',
        ]);

        try {
            foreach ($request->settings as $setting) {
                SaasSettings::setSetting($setting['key'], $setting['value'], $setting['type']);
            }

            Toastr::success('Settings updated successfully.', 'Success');

            return redirect()->route('saas.settings.index');
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }
}
