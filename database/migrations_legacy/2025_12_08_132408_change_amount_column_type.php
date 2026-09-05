<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAmountColumnType extends Migration
{
    public function up()
    {
        Schema::table('sm_books', function (Blueprint $table) {
            $table->double('book_price')->nullable()->change();
        });

        Schema::table('sm_routes', function (Blueprint $table) {
            $table->double('far')->nullable()->change();
        });

        Schema::table('sm_staffs', function (Blueprint $table) {
            $table->double('basic_salary')->nullable()->change();
        });

        Schema::table('sm_leave_deduction_infos', function (Blueprint $table) {
            $table->double('salary_deduct')->nullable()->change();
        });

        Schema::table('sm_hr_payroll_earn_deducs', function (Blueprint $table) {
            $table->double('amount')->nullable()->change();
        });

        Schema::table('sm_add_incomes', function (Blueprint $table) {
            $table->double('amount')->nullable()->change();
        });

        Schema::table('sm_add_expenses', function (Blueprint $table) {
            $table->double('amount')->nullable()->change();
        });

        Schema::table('sm_bank_statements', function (Blueprint $table) {
            $table->double('amount')->nullable()->change();
        });

        Schema::table('sm_fees_assigns', function (Blueprint $table) {
            $table->double('fees_amount')->nullable()->change();
        });

        Schema::table('sm_fees_discounts', function (Blueprint $table) {
            $table->double('amount')->nullable()->change();
        });

        Schema::table('sm_fees_payments', function (Blueprint $table) {
            $table->double('amount')->nullable()->change();
        });

        Schema::table('sm_fees_masters', function (Blueprint $table) {
            $table->double('amount')->nullable()->change();
        });

        Schema::table('sm_item_receives', function (Blueprint $table) {
            $table->double('grand_total')->nullable()->change();
            $table->double('total_paid')->nullable()->change();
            $table->double('total_due')->nullable()->change();
        });

        Schema::table('sm_item_receive_children', function (Blueprint $table) {
            $table->double('unit_price')->nullable()->change();
            $table->double('sub_total')->nullable()->change();
        });

        Schema::table('sm_item_sells', function (Blueprint $table) {
            $table->double('grand_total')->nullable()->change();
            $table->double('total_paid')->nullable()->change();
            $table->double('total_due')->nullable()->change();
        });

        Schema::table('sm_item_sell_children', function (Blueprint $table) {
            $table->double('sell_price')->nullable()->change();
            $table->double('sub_total')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('sm_books', function (Blueprint $table) {
            $table->integer('book_price')->change();
        });

        Schema::table('sm_routes', function (Blueprint $table) {
            $table->float('far')->change();
        });

        Schema::table('sm_staffs', function (Blueprint $table) {
            $table->string('basic_salary', 200)->change();
        });

        Schema::table('sm_leave_deduction_infos', function (Blueprint $table) {
            $table->integer('salary_deduct')->change();
        });

        Schema::table('sm_hr_payroll_earn_deducs', function (Blueprint $table) {
            $table->float('amount')->change();
        });

        Schema::table('sm_add_incomes', function (Blueprint $table) {
            $table->float('amount')->change();
        });

        Schema::table('sm_add_expenses', function (Blueprint $table) {
            $table->float('amount')->change();
        });

        Schema::table('sm_bank_statements', function (Blueprint $table) {
            $table->float('amount')->change();
        });

        Schema::table('sm_fees_assigns', function (Blueprint $table) {
            $table->float('fees_amount')->change();
        });

        Schema::table('sm_fees_discounts', function (Blueprint $table) {
            $table->float('amount')->change();
        });

        Schema::table('sm_fees_payments', function (Blueprint $table) {
            $table->float('amount')->change();
        });

        Schema::table('sm_fees_masters', function (Blueprint $table) {
            $table->float('amount')->change();
        });

        Schema::table('sm_item_receives', function (Blueprint $table) {
            $table->decimal('grand_total', 20, 2)->change();
            $table->decimal('total_paid', 20, 2)->change();
            $table->decimal('total_due', 20, 2)->change();
        });

        Schema::table('sm_item_receive_children', function (Blueprint $table) {
            $table->decimal('unit_price', 20, 2)->change();
            $table->decimal('sub_total', 20, 2)->change();
        });

        Schema::table('sm_item_sells', function (Blueprint $table) {
            $table->decimal('grand_total', 20, 2)->change();
            $table->decimal('total_paid', 20, 2)->change();
            $table->decimal('total_due', 20, 2)->change();
        });

        Schema::table('sm_item_sell_children', function (Blueprint $table) {
            $table->decimal('sell_price', 20, 2)->change();
            $table->decimal('sub_total', 20, 2)->change();
        });
    }
}
