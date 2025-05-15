<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableFbTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
        ALTER TABLE `fb_transactions` CHANGE COLUMN `sub_total_price` `sub_total_price` 
        DOUBLE
        DEFAULT NULL, CHANGE COLUMN `total_price` `total_price` 
        DOUBLE
        DEFAULT NULL
    ');

    DB::statement('
        ALTER TABLE `fb_transaction_details` CHANGE COLUMN `price` `price` 
        DOUBLE
        DEFAULT NULL, CHANGE COLUMN `amount` `amount` 
        DOUBLE
        DEFAULT NULL, CHANGE COLUMN `discount` `discount` 
        DOUBLE
        DEFAULT NULL, CHANGE COLUMN `max_discount_price` `max_discount_price` 
        DOUBLE
        DEFAULT NULL
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
        ALTER TABLE `fb_transactions` CHANGE COLUMN `sub_total_price` `sub_total_price` 
        DECIMAL(8,2)
        DEFAULT NULL,  CHANGE COLUMN `total_price` `total_price` 
        DECIMAL(8,2)
        DEFAULT NULL
    ');

    DB::statement('
        ALTER TABLE `fb_transaction_details` CHANGE COLUMN `price` `price` 
        DECIMAL(10,2)
        DEFAULT NULL, CHANGE COLUMN `amount` `amount` 
        DECIMAL(10,2)
        DEFAULT NULL, CHANGE COLUMN `discount` `discount` 
        DECIMAL(10,2)
        DEFAULT NULL, CHANGE COLUMN `max_discount_price` `max_discount_price` 
        DECIMAL(10,2)
        DEFAULT NULL
    ');
    }
}
