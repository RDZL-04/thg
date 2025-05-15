<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableFboutletMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('fboutlet_menus', function (Blueprint $table) {
        //     $table->enum('is_promo',['0','1'])->change();
        // });
        DB::statement('
        ALTER TABLE `fboutlet_menus` CHANGE COLUMN `is_promo` `is_promo` 
        ENUM("0", "1")
        DEFAULT "0"
    ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fboutlet_menus', function($table)
         {
            $table->binary('is_promo')->change();
        });
    }
}
