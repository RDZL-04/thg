<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSubmitProposal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submit_proposal', function (Blueprint $table) {
            $table->id();
            $table->integer('hall_id');
            $table->string('full_name',100);
            $table->string('email', 100);
            $table->string('phone', 15);
            $table->integer('capacity');
            $table->date('proposed_dt');
            $table->longText('additional_request')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('submit_proposal');
    }
}
