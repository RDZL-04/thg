<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnReservation extends Migration
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
            $table->float('be_amountAfterTax')->nullable()->change();
            $table->float('be_amountAfterTaxRoom')->nullable()->change();
            $table->float('be_amountBeforeTaxServ')->nullable()->change();
            $table->float('be_discount')->nullable()->change();
            $table->boolean('be_discountIndicator')->nullable()->change();
            $table->boolean('be_discountIndicatorRoom')->nullable()->change();
            $table->boolean('be_discountIndicatorServ')->nullable()->change();
            $table->float('be_discountRoom')->nullable()->change();
            $table->float('be_discountServ')->nullable()->change();
            $table->float('be_grossAmountBeforeTax')->nullable()->change();
            $table->float('be_grossAmountBeforeTaxRoom')->nullable()->change();
            $table->float('be_grossAmountBeforeTaxServ')->nullable()->change();
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
           $table->string('be_amountAfterTax')->nullable()->change();
            $table->string('be_amountAfterTaxRoom')->nullable()->change();
            $table->string('be_amountBeforeTaxServ')->nullable()->change();
            $table->string('be_discount')->nullable()->change();
            $table->string('be_discountIndicator')->nullable()->change();
            $table->string('be_discountIndicatorRoom')->nullable()->change();
            $table->string('be_discountIndicatorServ')->nullable()->change();
            $table->string('be_discountRoom')->nullable()->change();
            $table->string('be_discountServ')->nullable()->change();
            $table->string('be_grossAmountBeforeTax')->nullable()->change();
            $table->string('be_grossAmountBeforeTaxRoom')->nullable()->change();
            $table->string('be_grossAmountBeforeTaxServ')->nullable()->change();
        });
    }
}
