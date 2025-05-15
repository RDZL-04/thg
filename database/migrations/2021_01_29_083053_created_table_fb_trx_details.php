<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatedTableFbTrxDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fb_trx_details', function (Blueprint $table) {
            $table->id();
            $table->integer('trx_id');
            $table->integer('fb_menu_id');
            $table->decimal('price',$precision = 10,$scale = 2);
            $table->decimal('discount',$precision = 10,$scale = 2)->nullable();
            $table->integer('promo_id')->nullable();
            $table->decimal('promo_value',$precision = 5,$scale = 2)->nullable();
            $table->decimal('max_discount_price',$precision = 10,$scale = 2)->nullable();
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
        Schema::dropIfExists('fb_trx_details');
    }
}
