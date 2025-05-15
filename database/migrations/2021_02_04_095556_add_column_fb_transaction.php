<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFbTransaction extends Migration
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
            $table->decimal('tax')->after('customer_name')->nullable();
            $table->decimal('total_price')->after('tax')->nullable();
            $table->string('pg_payment_status',15)->after('payment_progress_sts')->nullable();
            $table->string('mpg_id')->after('table_no')->nullable();
            $table->string('mpg_url')->after('mpg_id')->nullable();
            
            
        });

        Schema::table('fb_transaction_details', function($table)
        {
            $table->integer('quantity')->after('fb_menu_id')->nullable();
            $table->string('note')->after('max_discount_price')->nullable();
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
            $table->dropColumn('total_price');
            $table->dropColumn('pg_payment_status');
            $table->dropColumn('mpg_id');
            $table->dropColumn('mpg_url');
            $table->dropColumn('tax');
        });

        Schema::table('fb_transaction_details', function($table)
        {
            $table->dropColumn('quantity');
            $table->dropColumn('note');
        });
    }
}
