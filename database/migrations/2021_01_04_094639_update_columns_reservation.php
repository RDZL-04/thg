<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnsReservation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservations', function($table)
        {
            $table->string('mpg_id',200)->nullable();
            $table->text('mpg_url')->nullable();
            $table->string('payment_sts',20)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservations', function($table)
        {
            $table->binary('payment_sts')->change();
            $table->dropColumn('mpg_id');
            $table->dropColumn('mpg_url');
        });
    }
}
