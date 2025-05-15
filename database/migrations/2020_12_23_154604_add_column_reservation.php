<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnReservation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservations', function($table)
        {
            $table->string('be_rate_plan_code')->nullable();
            $table->string('be_rate_plan_name')->nullable();
            $table->string('be_rate_plan_type')->nullable();
            $table->string('be_room_id')->nullable();
            $table->string('be_amountAfterTax')->nullable();
            $table->string('be_amountAfterTaxRoom')->nullable();
            $table->string('be_amountBeforeTaxServ')->nullable();
            $table->string('be_discount')->nullable();
            $table->string('be_discountIndicator')->nullable();
            $table->string('be_discountIndicatorRoom')->nullable();
            $table->string('be_discountIndicatorServ')->nullable();
            $table->string('be_discountRoom')->nullable();
            $table->string('be_discountServ')->nullable();
            $table->string('be_grossAmountBeforeTax')->nullable();
            $table->string('be_grossAmountBeforeTaxRoom')->nullable();
            $table->string('be_grossAmountBeforeTaxServ')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservations', function($table)
        {
            $table->dropColumn('be_rate_plan_code');
            $table->dropColumn('be_rate_plan_name');
            $table->dropColumn('be_rate_plan_type');
            $table->dropColumn('be_room_id');
            $table->dropColumn('be_amountAfterTax');
            $table->dropColumn('be_amountAfterTaxRoom');
            $table->dropColumn('be_amountBeforeTaxServ');
            $table->dropColumn('be_discount');
            $table->dropColumn('be_discountIndicator');
            $table->dropColumn('be_discountIndicatorRoom');
            $table->dropColumn('be_discountIndicatorServ');
            $table->dropColumn('be_discountRoom');
            $table->dropColumn('be_discountServ');
            $table->dropColumn('be_grossAmountBeforeTax');
            $table->dropColumn('be_grossAmountBeforeTaxRoom');
            $table->dropColumn('be_grossAmountBeforeTaxServ');

        });
    }
}
