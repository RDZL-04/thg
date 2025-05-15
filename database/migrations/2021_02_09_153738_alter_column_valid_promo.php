<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnValidPromo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promos', function($table)
         {
            $table->date('valid_from')->change();
            $table->date('valid_to')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promos', function($table)
        {
           $table->dateTime('valid_from')->change();
           $table->dateTime('valid_to')->change();
       });
    }
}
