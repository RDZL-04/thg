<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTaxFbTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
                ALTER TABLE `fb_transactions`
                CHANGE COLUMN `tax` `tax` DOUBLE NULL DEFAULT NULL AFTER `customer_name`; 
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('
            ALTER TABLE `fb_transactions` CHANGE COLUMN `tax` `tax` 
            DECIMAL(8,2)
            NULL DEFAULT NULL AFTER `customer_name`; 
        ');
    }
}
