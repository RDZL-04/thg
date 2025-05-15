<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name',100)->nullable()->change();
            $table->string('email',100)->change();
            $table->string('phone',15)->change();
            $table->boolean('jk')->change();
            $table->string('address',250)->change();
            $table->string('city',100)->change();
            $table->string('country',3)->change();
            $table->string('state_province',5)->change();
            $table->integer('postal_cd')->change();
            $table->string('password',100)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name')->change();
            $table->string('email')->change();
            $table->string('phone')->change();
            $table->string('jk')->change();
            $table->string('address')->change();
            $table->string('city')->change();
            $table->string('country')->change();
            $table->string('state_province')->change();
            $table->string('postal_cd')->change();
            $table->string('password')->change();
        });
    }
}
