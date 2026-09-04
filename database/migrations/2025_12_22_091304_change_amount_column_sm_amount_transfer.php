<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sm_amount_transfers', function (Blueprint $table) {
            $table->double('amount')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('sm_amount_transfers', function (Blueprint $table) {
            $table->integer('amount')->change();
        });
    }
};
