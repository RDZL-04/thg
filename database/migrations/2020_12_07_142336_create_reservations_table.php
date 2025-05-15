<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_no');            
            $table->integer('hotel_id');
            $table->string('be_hotel_id');
            $table->integer('be_room_pkg_id');
            $table->string('be_room_type_nm');
            $table->string('be_room_pkg_nm');
            $table->dateTime('checkin_dt');
            $table->dateTime('checkout_dt');
            $table->integer('ttl_adult')->nullable();
            $table->integer('ttl_children')->nullable();
            $table->integer('ttl_room')->nullable();
            $table->binary('is_member')->nullable();
            $table->integer('customer_id');
            $table->integer('payment_method_id');
            $table->binary('payment_sts');
            $table->double('price');
            $table->double('tax')->nullable();
            $table->integer('allo_point')->nullable();
            $table->integer('allo_coupons_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}
