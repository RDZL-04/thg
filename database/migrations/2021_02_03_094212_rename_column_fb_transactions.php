<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnFbTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fb_transactions', function($table)
        {
            $table->renameColumn('trx_no', 'transaction_no');
        });

        Schema::table('fb_transaction_details', function($table)
        {
            $table->renameColumn('trx_id', 'transaction_id');
        });

        Schema::table('fb_transaction_sdishs', function($table)
        {
            $table->renameColumn('fb_trx_detail_id', 'fb_transaction_detail_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fb_transactions', function($table)
        {
            $table->renameColumn('transaction_no', 'trx_no');
        });

        Schema::table('fb_transaction_details', function($table)
        {
            $table->renameColumn('transaction_id', 'trx_id');
        });

        Schema::table('fb_transaction_sdishs', function($table)
        {
            $table->renameColumn('fb_transaction_detail_id', 'fb_trx_detail_id');
        });
    }
}
