<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RemoveSidebarManagerFromTeanchsAndStudent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::table('permissions')->where('route', 'menumanage.index')->where('is_parent', 1)->delete();
        DB::table('permissions')->where('route', 'menumanage.index')->where('is_student', 1)->delete();
        DB::table('permissions')->where('route', 'dormitory-list-store')->where('is_admin', 1)->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
}
