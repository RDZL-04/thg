<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByAndUpdatedByOnTableFboutletMnCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fboutlet_mn_categories', function($table)
        {
            $table->string('created_by',25)->after('created_at')->nullable();
            $table->string('updated_by',25)->after('updated_at')->nullable();
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
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
}
