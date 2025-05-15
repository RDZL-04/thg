<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOpenCloseOutlet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fboutlets', function($table)
        {
            $table->time('open_at')->nullable()->after('service');
            $table->time('close_at')->nullable()->after('open_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fboutlets', function($table)
        {
            $table->dropColumn('open_at');
            $table->dropColumn('close_at');
        });
    }
}
