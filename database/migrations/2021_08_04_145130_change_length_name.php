<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeLengthName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('name',100)->change();
            $table->string('address',200)->change();
        });

        Schema::table('fboutlets', function (Blueprint $table) {
            $table->string('name',100)->change();
            $table->string('address',200)->change();
        });

        Schema::table('facilities', function (Blueprint $table) {
            $table->string('name',100)->change();
        });

        Schema::table('fboutlet_menus', function (Blueprint $table) {
            $table->string('name',150)->change();
        });

        Schema::table('fboutlet_mn_categories', function (Blueprint $table) {
            $table->string('name',150)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('name')->change();
            $table->string('address')->change();
        });

        Schema::table('fboutlets', function (Blueprint $table) {
            $table->string('name')->change();
            $table->string('address')->change();
        });

        Schema::table('facilities', function (Blueprint $table) {
            $table->string('name')->change();
        });

        Schema::table('fboutlet_menus', function (Blueprint $table) {
            $table->string('name')->change();
        });

        Schema::table('fboutlet_mn_categories', function (Blueprint $table) {
            $table->string('name')->change();
        });
    }
}
