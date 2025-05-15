<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShowCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fboutlet_mn_categories', function (Blueprint $table) {
            $table->tinyInteger('show_in_menu')->default(1)->after('seq_no');
        });
        Schema::table('fboutlet_menus', function($table){
            $table->string('images')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fboutlet_mn_categories', function (Blueprint $table) {
            $table->dropColumn('show_in_menu');
        });
        Schema::table('fboutlet_menus', function($table){
            $table->string('images')->change();
        });
    }
}
