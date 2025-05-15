<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHallImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hall_images', function (Blueprint $table) {
            $table->id();
            $table->integer('hall_id');
            $table->string('name',50);
            $table->string('filename',255);
            $table->integer('seq');
            $table->binary('status');
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
        Schema::dropIfExists('hall_images');
    }
}
