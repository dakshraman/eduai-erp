<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('oauth_clients')) {
            return;
        }

        if (! Schema::hasColumn('oauth_clients', 'grant_types')) {
            Schema::table('oauth_clients', function (Blueprint $table): void {
                $table->text('grant_types')->nullable();
            });
        }

        if (Schema::hasColumn('oauth_clients', 'personal_access_client')) {
            DB::table('oauth_clients')
                ->where('personal_access_client', 1)
                ->update(['grant_types' => json_encode(['personal_access'])]);
        } else {
            DB::table('oauth_clients')
                ->where('name', 'like', '%Personal Access%')
                ->update(['grant_types' => json_encode(['personal_access'])]);
        }

        if (Schema::hasColumn('oauth_clients', 'password_client')) {
            DB::table('oauth_clients')
                ->where('password_client', 1)
                ->update(['grant_types' => json_encode(['password', 'refresh_token'])]);
        } else {
            DB::table('oauth_clients')
                ->where('name', 'like', '%Password Grant%')
                ->update(['grant_types' => json_encode(['password', 'refresh_token'])]);
        }
    }

    public function down(): void
    {
        //
    }
};
