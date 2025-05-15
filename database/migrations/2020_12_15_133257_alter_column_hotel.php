<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnHotel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotels', function(Blueprint $t) {
            $t->decimal('longitude', $precision = 9, $scale = 6)->nullable()->change();
            $t->decimal('latitude', $precision = 9, $scale = 6)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotels', function(Blueprint $t) {
            $table->string('longitude')->change();
            $table->string('latitude')->change();
        });
    }
}
