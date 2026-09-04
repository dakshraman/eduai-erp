<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sm_backups', function (Blueprint $table): void {
            if (! Schema::hasColumn('sm_backups', 'storage_type')) {
                $table->string('storage_type')->nullable()->default('local')->after('file_type')->comment('local, google, contabo');
            }

            if (! Schema::hasColumn('sm_backups', 'storage_link')) {
                $table->text('storage_link')->nullable()->after('storage_type');
            }

            if (! Schema::hasColumn('sm_backups', 'is_asset')) {
                $table->boolean('is_asset')->default(0)->after('storage_link')->comment('0=DB, 1=Files');
            }
        });

        DB::table('sm_backups')
            ->whereNull('storage_type')
            ->update(['storage_type' => 'local']);
    }

    public function down(): void
    {
        // Intentionally no-op. These columns are used by newer backup features and
        // may also be created by the original storage-field migration.
    }
};
