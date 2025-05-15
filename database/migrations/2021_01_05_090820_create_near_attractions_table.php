<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNearAttractionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('near_attractions', function (Blueprint $table) {
            $table->id();
            $table->integer('hotel_id');
            $table->string('attraction_nm',100);
            $table->integer('category_id');
            $table->string('distance',50);
            $table->string('created_by',30);
            $table->string('changed_by',30);
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
        Schema::dropIfExists('near_attractions');
    }
}
