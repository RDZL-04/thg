<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnReservation extends Migration
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
            $table->string('be_room_pkg_id')->nullable()->change();
            $table->string('ttl_children')->nullable()->change();
            $table->string('payment_sts')->nullable()->change();
            $table->string('allo_point')->nullable()->change();
            $table->string('allo_coupons_id')->nullable()->change();
            $table->string('mpg_id')->nullable()->change();
            $table->string('mpg_url')->nullable()->change();
            $table->renameColumn('spesial_request', 'special_request');
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
            $table->string('be_room_pkg_id')->nullable(false)->change();
            $table->string('ttl_children')->nullable(false)->change();
            $table->string('payment_sts')->nullable(false)->change();
            $table->string('allo_point')->nullable(false)->change();
            $table->string('allo_coupons_id')->nullable(false)->change();
            $table->string('mpg_id')->nullable(false)->change();
            $table->string('mpg_url')->nullable(false)->change();
            $table->renameColumn('special_request', 'spesial_request');
        });
    }
}
