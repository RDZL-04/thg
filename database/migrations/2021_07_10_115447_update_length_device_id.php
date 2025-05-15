<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLengthDeviceId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fb_transactions', function (Blueprint $table) {
            $table->string('device_id',50)->nullable()->after('hold_at')->change();
        });
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('device_id',50)->nullable()->after('hold_at')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fb_transactions', function (Blueprint $table) {
            $table->string('device_id',25)->nullable()->after('hold_at')->change();
        });
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('device_id',25)->nullable()->after('hold_at')->change();
        });
    }
}
