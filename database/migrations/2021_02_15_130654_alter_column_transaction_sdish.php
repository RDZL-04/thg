<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnTransactionSdish extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fb_transaction_sdishs', function($table)
         {
            $table->integer('fb_menu_sdish_id')->nullable()->change();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fb_transaction_sdishs', function($table)
         {
            $table->decimal('fb_menu_sdish_id',$precision = 10,$scale = 2)->nullable()->change();
        });
    }
}
