<?php

namespace App\Console\Commands;

use App\Models\InfixModuleManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Nwidart\Modules\Facades\Module;

class ResetApplicationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:application';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset application to default state';

    protected $modules = [
//        'Saas',
        'Zoom',
        'ParentRegistration',
        'RazorPay',
        'Jitsi',
        'XenditPayment',
        'KhaltiPayment',
        'Raudhahpay',
        'InfixBiometrics',
        'Gmeet',
        'PhonePay',
        'AiContent',
        'WhatsappSupport',
        'Certificate',
        'InAppLiveClass',
        'MercadoPago',
        'BBB',
        'QRCodeAttendance',
        'Lms',
        'Branch'
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->call('down');
        $this->createAppResetFile();
        $this->deativateActivatedModule();
        $this->call('optimize:clear');
        $this->call('migrate:fresh', ['--force' => true]);
        $this->moduleAcivate();
        $this->dbSeed();
        $this->call('optimize:clear');
        $this->call('clear:log-files');
        $this->generateNewKey();
        $this->deleteAppResetFile();
        $this->call('up');
    }

    public function createAppResetFile(): void
    {
        Storage::put('.app_resetting', '');
        Storage::put('.reset_log', now()->toDateTimeString());
    }

    protected function deleteAppResetFile()
    {
        Storage::delete('.app_resetting');
    }

    protected function generateNewKey()
    {
        Artisan::call('key:generate', ['--force' => true]);
    }

    protected function getModulesList(): array
    {
        $modules = $this->modules;
        if (moduleStatusCheck('Saas') || Module::find('Saas')) {
            if (!in_array('Saas', $modules)) {
                array_unshift($modules, 'Saas');
            }
        }
        return $modules;
    }

    protected function deativateActivatedModule()
    {
        foreach ($this->getModulesList() as $module) {
            $m = Module::find($module);
            if ($m) {
                $m->disable();
            }
        }
    }

    protected function moduleAcivate()
    {
        foreach ($this->getModulesList() as $data) {
            $module = InfixModuleManager::where('name', $data)->first();
            if ($module) {
                $module->purchase_code = 986532741;
                $module->save();
            }

            config(['app.app_sync' => false]);
            $controller = new \App\Http\Controllers\Admin\SystemSettings\SmAddOnsController();
            $controller->moduleAddOnsEnable($data);
            config(['app.app_sync' => true]);
        }
    }

    protected function dbSeed()
    {
        Artisan::call('db:seed', ['--force' => true]);
        if (moduleStatusCheck('Lms') || Module::find('Lms')) {
            Artisan::call('module:seed', ['module' => 'Lms', '--force' => true]);
        }
        if (moduleStatusCheck('Saas') || Module::find('Saas')) {
            Artisan::call('module:seed', ['module' => 'Saas', '--force' => true]);
        }
    }
}
