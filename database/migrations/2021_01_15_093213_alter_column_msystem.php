<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnMsystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('msystems', function($table)
         {
            $table->string('system_type',100)->change();
            $table->string('system_cd',100)->change();
            $table->string('system_value',255)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('msystems',function($table)
         {
            $table->string('system_type')->change();
            $table->string('system_cd')->change();
            $table->string('system_value')->change();
        });
    }
}
