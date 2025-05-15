<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxFboutlet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fboutlets', function($table)
        {
            $table->integer('tax')->after('mpg_secret_key')->nullable();
            $table->integer('service')->after('tax')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fboutlets', function($table)
        {
            $table->dropColumn('tax');
            $table->dropColumn('service');
            
        });
    }
}
