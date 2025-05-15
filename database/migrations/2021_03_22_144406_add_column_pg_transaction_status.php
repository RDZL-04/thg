<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPgTransactionStatus extends Migration
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
            $table->string('pg_transaction_status',15)->after('pg_payment_status')->default(null)->nullable();
        });

        Schema::table('reservations', function($table)
        {
            $table->string('pg_transaction_status',15)->after('payment_sts')->default(null)->nullable();
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
            $table->dropColumn('pg_transaction_status');
        });

        Schema::table('reservations', function($table)
        {
            $table->dropColumn('pg_transaction_status');
        });
    }
}
