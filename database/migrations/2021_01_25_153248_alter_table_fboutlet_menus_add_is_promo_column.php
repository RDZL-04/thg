<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableFboutletMenusAddIsPromoColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fboutlet_menus', function($table)
        {
            $table->binary('is_promo')->after('menu_sts')->nullable();
        });
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
            $table->dropColumn('is_promo');
        });
    }
}
