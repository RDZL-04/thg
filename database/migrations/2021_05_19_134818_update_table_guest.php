<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableGuest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guests', function($table)
        {
            $table->string('guest_country',3)->nullable()->change();
            $table->string('guest_state_province',100)->nullable()->change();
            $table->string('guest_city',100)->nullable()->change();
            $table->string('guest_postal_cd',10)->nullable()->change();
            $table->string('city',100)->nullable()->change();
            $table->string('country',3)->nullable()->change();
            $table->string('state_province',100)->nullable()->change();
            $table->string('postal_cd',10)->nullable()->change();
            $table->text('guest_address')->nullable()->change();
            $table->text('address')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guests', function($table)
        {
            $table->string('guest_country',3)->change();
            $table->string('guest_state_province',100)->change();
            $table->string('guest_city',100)->change();
            $table->string('guest_postal_cd',10)->change();
            $table->text('address')->change();
            $table->text('guest_address')->change();
            $table->string('city',100)->change();
            $table->string('country',3)->change();
            $table->string('state_province',100)->change();
            $table->string('postal_cd',10)->change();
        });
    }
}
