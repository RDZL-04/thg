<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColoumUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('users', function($table)
        {
            $table->string('address')->after('image')->nullable();
            $table->string('city')->after('address')->nullable();
            $table->string('country')->after('city')->nullable();
            $table->string('state_province')->after('country')->nullable();
            $table->string('postal_cd')->after('state_province')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
