<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFboutletMnCategoriesAddFbOutletId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fboutlet_mn_categories', function ($table) {
			$table->integer('fboutlet_id')->after('name');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fboutlet_mn_categories', function($table)
        {
            $table->dropColumn('fboutlet_id');
        });
    }
}
