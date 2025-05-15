<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatedTableFbSdishs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fb_trx_sdishs', function (Blueprint $table) {
            $table->id();
            $table->integer('fb_trx_detail_id');
            $table->integer('fb_menu_id');
            $table->decimal('fb_menu_sdish_id',$precision = 10,$scale = 2)->nullable();
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
        Schema::dropIfExists('fb_trx_sdishs');
    }
}
