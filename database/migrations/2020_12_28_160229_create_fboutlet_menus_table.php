<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFboutletMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fboutlet_menus', function (Blueprint $table) {
            $table->id();
            $table->integer('fboutlet_id');
            $table->string('name');
            $table->string('description');
            $table->decimal('price', 10, 2);
            $table->string('images');
            $table->binary('menu_sts');
            $table->integer('menu_cat_id');
            $table->integer('seq_no');
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('changed_by')->nullable();
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
        Schema::dropIfExists('fboutlet_menus');
    }
}
