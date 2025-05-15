<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnOsTypeReservation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
        ALTER TABLE `reservations`
	    ADD COLUMN `os_type` ENUM("WEB","MOBILE") NULL DEFAULT NULL AFTER `be_discountCode`;
        ');
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
            $table->dropColumn('os_type');
        });
    }
}
