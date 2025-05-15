<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatedTableErrorLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('error_log', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address',12);
            $table->string('username',50)->nullable();
            $table->string('modul',50)->nullable();
            $table->string('actions',50)->nullable();
            $table->text('error_log')->nullable();
            $table->timestamp('error_date');
            $table->enum('device', array(0, 2))->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('error_log');
    }
}
