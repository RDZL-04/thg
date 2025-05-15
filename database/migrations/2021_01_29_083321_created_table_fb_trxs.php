<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatedTableFbTrxs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fb_trxs', function (Blueprint $table) {
            $table->id();
            $table->string('trx_no',25);
            $table->integer('is_member')->nullable();
            $table->integer('customer_id')->nullable();
            $table->string('customer_name',100)->nullable();
            $table->string('payment_method_id',10)->nullable();
            $table->string('payment_progress_sts',1)->nullable();
            $table->integer('approver_id')->nullable();
            $table->string('table_no',10)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fb_trxs');
    }
}
