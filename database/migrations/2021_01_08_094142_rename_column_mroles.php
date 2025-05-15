<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnMroles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mroles', function($table)
        {
            $table->renameColumn('changed_by', 'updated_by');
            $table->string('description',100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mroles', function($table)
        {
            $table->renameColumn('updated_by', 'changed_by');
            $table->dropColumn('description');
        });
    }
}
