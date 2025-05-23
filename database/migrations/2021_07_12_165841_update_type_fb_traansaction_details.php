<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTypeFbTraansactionDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fb_transaction_details', function (Blueprint $table) {
            $table->text('note')->nullable()->after('max_discount_price')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fb_transaction_details', function (Blueprint $table) {
            $table->string('note')->nullable()->after('max_discount_price')->change();
        });
    }
}
