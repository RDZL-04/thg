<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTrx extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('fb_trxs', 'fb_transactions');
        Schema::rename('fb_trx_details', 'fb_transaction_details');
        Schema::rename('fb_trx_sdishs', 'fb_transaction_sdishs');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('fb_transactions', 'fb_trxs');
        Schema::rename('fb_transaction_details', 'fb_trx_details');
        Schema::rename('fb_transaction_sdishs', 'fb_trx_sdishs');
    }
}
