<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class MakeNullablePaymentNoteColumnOnfmFeesTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('fm_fees_transactions', 'payment_note')) {
            Schema::table('fm_fees_transactions', function ($table) {
                $table->text('payment_note', 150)->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
}
