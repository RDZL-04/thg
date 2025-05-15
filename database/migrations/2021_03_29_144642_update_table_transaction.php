<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservations', function($table)
        {
            $table->dropColumn('payment_method_id');
        });

        Schema::table('fb_transactions', function($table)
        {
            $table->renameColumn('payment_method_id', 'payment_source');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservations', function($table)
        {
            $table->integer('payment_method_id');
        });

        Schema::table('fb_transactions', function($table)
        {
            $table->renameColumn('payment_source', 'payment_method_id');
        });
    }
}
