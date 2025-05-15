<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMiceHalls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mice_halls', function (Blueprint $table) {
            $table->id();
            $table->string('name',50);
            $table->longText('descriptions');
            $table->integer('capacity');
            $table->integer('size');
            $table->string('layout',255);
            $table->integer('seq');
            $table->string('created_by',25)->nullable();
            $table->string('updated_by',25)->nullable();
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
        Schema::dropIfExists('mice_halls');
    }
}
