<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnOsTypeFbTransaction extends Migration
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
	    ADD COLUMN `os_type` ENUM("WEB","MOBILE") NULL DEFAULT NULL AFTER `mpg_url`;
    ');
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fb_transactions', function($table)
        {
            $table->dropColumn('os_type');
        });
    }
}
