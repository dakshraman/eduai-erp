<?php

namespace App\Http\Controllers\Admin\SystemSettings;

use App\Envato\Envato;
use App\Http\Controllers\Controller;
use App\Models\InfixModuleManager;
use App\Models\SmGeneralSettings;
use App\Support\ModuleRegistry;
use App\Traits\RestartOctan;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Nwidart\Modules\Facades\Module;
use Throwable;

class SmAddOnsController extends Controller
{
    use RestartOctan;

    protected $systemConfigModule = 'FeesCollection';

    public function ModuleRefresh()
    {
        Artisan::call('optimize:clear');
        ModuleRegistry::invalidate();
        $this->reloadOctane();
        Toastr::success('Refresh successful', 'Success');

        return redirect()->back();
    }

    public function ManageAddOns()
    {
        $module_list = [];
        $is_module_available = Module::all();

        return view('backEnd.systemSettings.ManageAddOns', ['is_module_available' => $is_module_available, 'module_list' => $module_list]);
    }

    public function moduleAddOnsEnable(string $name)
    {
        if (config('app.app_sync')) {
            return response()->json(['error' => 'Restricted in demo mode']);
        }

        $moduleCheck = $this->findModule($name);
        if (! $moduleCheck) {
            return response()->json(['error' => 'Module not found'], 404);
        }

        $metadata = $this->moduleMetadata($name);
        if (! $metadata) {
            return response()->json(['error' => 'Module configuration not found'], 422);
        }

        try {
            if ($moduleCheck->isDisabled()) {
                $moduleCheck->enable();

                if (! $this->moduleMigration($name)) {
                    $moduleCheck->disable();
                    $this->setModuleSetting($name, 0);
                    $this->forgetModuleCache($name);

                    return response()->json(['error' => 'Module migration failed'], 422);
                }

                DB::transaction(function () use ($metadata, $name): void {
                    $this->upsertModuleManager($name, $metadata);
                    $this->setModuleSetting($name, 1);
                });

                $this->forgetModuleCache($name);
                $this->reloadOctane();

                return response()->json([
                    'data' => 'enable',
                    'success' => 'Operation success! Thanks you.',
                ], 200);
            }

            $moduleCheck->disable();
            $this->setModuleSetting($name, 0);
            $this->forgetModuleCache($name);
            $this->reloadOctane();

            return response()->json([
                'data' => 'disable',
                'Module' => $moduleCheck,
                'success' => 'Operation success! Thanks you.',
            ], 200);
        } catch (Throwable $exception) {
            Log::info($exception->getMessage());
            return response()->json(['error' => $exception->getMessage()]);
        }
    }

    public function moduleAddOnsDisable(string $name)
    {
        if (config('app.app_sync')) {
            return response()->json(['error' => 'Restricted in demo mode']);
        }

        $moduleCheck = $this->findModule($name);
        if (! $moduleCheck) {
            return response()->json(['error' => 'Module not found'], 404);
        }

        $moduleCheck->disable();
        $this->setModuleSetting($name, 0);
        $this->forgetModuleCache($name);
        $this->reloadOctane();

        return response()->json([
            'data' => 'disable',
            'Module' => $moduleCheck,
            'success' => 'Operation success! Thanks you.',
        ], 200);
    }

    protected function clearCache(){
        try {
            \Artisan::call('optimize:clear');
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    public function ManageAddOnsValidation(Request $request)
    {
        $request->validate([
            'purchase_code' => 'required',
            'name' => 'required',
        ]);

        $code = $request->purchase_code;
        $email = $request->email;
        $name = $request->name;

        if (! $this->findModule($name)) {
            Toastr::error('Module not found', 'Failed');
            return redirect()->back();
        }

        if (Config::get('app.app_pro') && $request->email== '') {
            Toastr::error('Email is required', 'Failed');

            return redirect()->back();
        }

        if (Config::get('app.app_pro')) {
            try {
                $client = new Client([
                    'connect_timeout' => 5,
                    'timeout' => 10,
                ]);
                $product_info = $client->request('GET', 'https://sp.uxseven.com/api/module/'.$code.'/'.$email, [
                    'http_errors' => false,
                ]);
                $product_info = json_decode($product_info->getBody()->getContents());
            } catch (Throwable $exception) {
                Log::info($exception->getMessage());
                Toastr::error('Unable to verify purchase code. Please try again.', 'Failed');

                return redirect()->back();
            }

            if (! empty($product_info->products[0])) {
                try {
                    $this->activateVerifiedModule($name, $email, $request->purchase_code);
                    Toastr::success('Verification successful', 'Success');

                    return redirect()->back();
                } catch (Throwable $e) {
                    $this->deactivateModule($name);
                    Toastr::error($e->getMessage(), 'Failed');

                    return redirect()->back();
                }
            }

            $this->deactivateModule($name);
            Toastr::error('Ops! Purchase code is not valid.', 'Failed');

            return redirect()->back();
        }
        $email = $request->envatouser;
        $UserData = Envato::verifyPurchase($request->purchase_code);

        if (! empty($UserData['verify-purchase']['item_id'])) {
            try {
                $this->activateVerifiedModule($name, $email, $request->purchase_code);
                Toastr::success('Verification successful', 'Success');

                return redirect()->back();
            } catch (Throwable $e) {
                $this->deactivateModule($name);
                Toastr::error($e->getMessage(), 'Failed');

                return redirect()->back();
            }
        } else {
            $this->deactivateModule($name);
            Toastr::error('Ops! Purchase code is not valid.', 'Failed');

            return redirect()->back();
        }

    }

    public function FreemoduleAddOnsEnable(string $name): void
    {
        session()->forget('all_module');
        Cache::forget('module_'.$name);
        $moduleCheck = Module::find($name);
        if (! $moduleCheck) {
            Log::info('module not found');
            return;
        }

        try {
            $metadata = $this->moduleMetadata($name);
            if (! $metadata) {
                Log::info('module configuration not found');
                return;
            }

            $is_module_available = module_path($name, 'Providers/'.$name.'ServiceProvider.php');

            if (file_exists($is_module_available)) {
                $moduleCheck->enable();
                if (! $this->moduleMigration($name)) {
                    $moduleCheck->disable();
                    $this->setModuleSetting($name, 0);
                    $this->forgetModuleCache($name);
                    return;
                }

                DB::transaction(function () use ($metadata, $name): void {
                    $this->upsertModuleManager($name, $metadata);
                    $this->setModuleSetting($name, 1);
                });

                $this->forgetModuleCache($name);
            } else {
                Log::info('module not found');
                $moduleCheck->disable();
            }

        } catch (Throwable $exception) {
            $moduleCheck->disable();
            $this->setModuleSetting($name, 0);
            $this->forgetModuleCache($name);
            Log::info($exception->getMessage());
        }
    }

    public function moduleMigration($module): bool
    {
        try {

            Artisan::call('module:migrate', [
                'module' => $module,
                '--force' => true,
            ]);
            $this->publishAssets($module);
            $this->reloadOctane();

            return true;
        } catch (Throwable $exception) {
            Log::info($exception);
            return false;
        }

    }

    private function publishAssets($module)
    {
        Artisan::call('module:publish', [
            'module' => $module,
        ]);
    }

    private function findModule(string $name)
    {
        if (! preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $name)) {
            return null;
        }

        $module = Module::find($name);
        if (! $module) {
            return null;
        }

        $provider = module_path($name, 'Providers/'.$name.'ServiceProvider.php');

        return file_exists($provider) ? $module : null;
    }

    private function moduleMetadata(string $name): ?array
    {
        $dataPath = module_path($name, $name.'.json');
        if (! $dataPath || ! file_exists($dataPath)) {
            return null;
        }

        $array = json_decode(file_get_contents($dataPath), true);
        if (! is_array($array) || empty($array[$name])) {
            return null;
        }

        return [
            'notes' => $array[$name]['notes'][0] ?? '',
            'version' => $array[$name]['versions'][0] ?? '',
            'update_url' => $array[$name]['url'][0] ?? '',
        ];
    }

    private function upsertModuleManager(string $name, array $metadata, ?string $email = null, ?string $purchaseCode = null): void
    {
        $module = InfixModuleManager::firstOrNew(['name' => $name]);
        $module->notes = $metadata['notes'] ?? '';
        $module->version = $metadata['version'] ?? '';
        $module->update_url = $metadata['update_url'] ?? '';
        $module->installed_domain = url('/');
        $module->activated_date = now()->toDateString();

        if ($email !== null) {
            $module->email = $email;
        }

        if ($purchaseCode !== null) {
            $module->purchase_code = $purchaseCode;
        }

        $module->save();
    }

    private function activateVerifiedModule(string $name, ?string $email, string $purchaseCode): void
    {
        $metadata = $this->moduleMetadata($name);
        if (! $metadata) {
            throw new Exception('Module configuration not found');
        }

        $this->ensureGeneralSettingColumn($name);

        DB::transaction(function () use ($email, $metadata, $name, $purchaseCode): void {
            $this->upsertModuleManager($name, $metadata, $email, $purchaseCode);
            $this->setModuleSetting($name, 1);
        });

        $this->forgetModuleCache($name);
    }

    private function deactivateModule(string $name): void
    {
        $module = Module::find($name);
        if ($module) {
            $module->disable();
        }

        $this->setModuleSetting($name, 0);
        $this->forgetModuleCache($name);
    }

    private function ensureGeneralSettingColumn(string $name): void
    {
        if (! Schema::hasColumn('sm_general_settings', $name)) {
            Schema::table('sm_general_settings', function ($table) use ($name): void {
                $table->integer($name)->default(1)->nullable();
            });
        }
    }

    private function setModuleSetting(string $name, int $status): void
    {
        if (! Schema::hasColumn('sm_general_settings', $name)) {
            return;
        }

        $config = SmGeneralSettings::first();
        if (! $config) {
            return;
        }

        $config->$name = $status;
        $config->save();
    }

    private function forgetModuleCache(string $name): void
    {
        Cache::forget('module_'.$name);
        Cache::forget('paid_modules');
        Cache::forget('default_modules');
        session()->forget('all_module');
        ModuleRegistry::invalidate();
        $this->clearCache();
    }
}
