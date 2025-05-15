<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMacAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('fb_transactions', function($table)
        {
            $table->renameColumn('mac_address', 'device_id');
        });
        Schema::table('reservations', function($table)
        {
            $table->renameColumn('mac_address', 'device_id');
        });
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
            $table->renameColumn('device_id', 'mac_address');
        });
        Schema::table('reservations', function($table)
        {
            $table->renameColumn('device_id', 'mac_address');
        });
    }
}
