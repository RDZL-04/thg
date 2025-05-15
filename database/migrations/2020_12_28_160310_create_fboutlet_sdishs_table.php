<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFboutletSdishsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fboutlet_mn_sdishs', function (Blueprint $table) {
            $table->id();
            $table->integer('fboutlet_mn_id');
            $table->integer('fboutlet_mn_sdish_id');
            $table->binary('is_sidedish');
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
        Schema::dropIfExists('fboutlet_sdishs');
    }
}
