<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnsHotels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotels', function($table)
        {
            $table->renameColumn('be_secreet_key', 'be_secret_key');
            $table->renameColumn('mpg_secreet_key', 'mpg_secret_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotels', function($table)
        {
            $table->renameColumn('be_secret_key', 'be_secreet_key');
            $table->renameColumn('mpg_secret_key', 'mpg_secreet_key');
        });
    }
}
