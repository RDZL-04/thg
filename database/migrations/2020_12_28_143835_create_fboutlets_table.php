<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFboutletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fboutlets', function (Blueprint $table) {
            $table->id();
            $table->integer('hotel_id');
            $table->string('name');
            $table->string('address');
            $table->decimal('longitude', $precision = 9, $scale = 6)->nullable();
            $table->decimal('latitude', $precision = 9, $scale = 6)->nullable();
            $table->string('description');
            $table->binary('status');
            $table->integer('seq_no');
            $table->string('mpg_merchant_id')->nullable();
            $table->string('mpg_api_key')->nullable();
            $table->string('mpg_secret_key')->nullable();
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
        Schema::dropIfExists('fboutlets');
    }
}
