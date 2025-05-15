<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableReservation extends Migration
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
            $table->string('transaction_no',30)->change();
            $table->string('be_hotel_id',10)->change();
            $table->string('be_room_id',15)->change();
            $table->string('be_room_type_nm',150)->change();
            $table->string('be_rate_plan_code',15)->change();
            $table->string('be_room_pkg_id',15)->change();
            $table->string('be_room_pkg_nm',150)->change();
            $table->integer('ttl_children')->change();
            $table->decimal('allo_point')->change();
            $table->integer('allo_coupons_id')->change();
            $table->string('currency',3)->change();
            $table->string('payment_sts',50)->change();

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
            $table->string('transaction_no')->change();
            $table->string('be_hotel_id')->change();
            $table->string('be_room_id')->change();
            $table->string('be_room_type_nm')->change();
            $table->string('be_rate_plan_code')->change();
            $table->string('be_room_pkg_id')->change();
            $table->string('be_room_pkg_nm')->change();
            $table->string('ttl_children')->change();
            $table->string('allo_point')->change();
            $table->string('allo_coupons_id')->change();
            $table->string('currency')->change();
            $table->string('payment_sts')->change();
        });
    }
}
