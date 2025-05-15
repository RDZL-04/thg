<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFbTransactionDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fb_transaction_details', function($table)
        {
            $table->bigInteger('parent_id')->after('id')->default(null)->nullable();
            $table->decimal('amount',$precision = 10,$scale = 2)->after('price')->nullable();
        });
        DB::statement('
        ALTER TABLE `fb_transaction_details` ADD COLUMN `is_sidedish` ENUM("0", "1") NULL
        DEFAULT NULL AFTER `fb_menu_id`
    ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fb_transaction_details', function($table)
        {
            $table->dropColumn('parent_id');
            $table->dropColumn('amount');
            $table->dropColumn('is_sidedish');
        });
    }
}
