<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            [
                'module' => '',
                'name' => 'import',
                'parent_route' => 'assign_subject',
                'lang_name' => '',
                'route' => 'assign_subject_import',
                'status' => 1,
                'menu_status' => 1,
                'position' => 5,
                'is_saas' => 0,
                'relate_to_child' => 0,
                'is_menu' => 0,
                'is_admin' => 1,
                'type' => 3,
                'parent_id' => 0,
                'permission_section' => 0,
                'school_id' => 1,
            ],

        ];
        foreach ($permissions as $permission) {
            DB::table('permissions')->where('route', $permission['route'])->delete();
            DB::table('permissions')->insert($permission);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assign_subject_import');
    }
};
