<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnGuest extends Migration
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
            $table->string('guest_full_name',100);
            $table->string('guest_phone',15);
            $table->string('guest_email',50);
            $table->string('guest_country',3);
            $table->string('guest_state_province',100);
            $table->string('guest_city',100);
            $table->text('guest_address');
            $table->string('guest_postal_cd',10);
            $table->string('full_name',100)->change();
            $table->string('phone',15)->change();
            $table->string('email',50)->change();
            $table->text('address')->change();
            $table->string('city',100)->change();
            $table->string('country',3)->change();
            $table->string('state_province',100)->change();
            $table->string('postal_cd',10)->change();
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
            $table->dropColumn('guest_full_name');
            $table->dropColumn('guest_phone');
            $table->dropColumn('guest_email');
            $table->dropColumn('guest_country');
            $table->dropColumn('guest_state_province');
            $table->dropColumn('guest_city');
            $table->dropColumn('guest_address');
            $table->dropColumn('guest_postal_cd');
            $table->string('full_name')->change();
            $table->string('phone')->change();
            $table->string('email')->change();
            $table->string('address')->change();
            $table->string('city')->change();
            $table->string('country')->change();
            $table->string('state_province')->change();
            $table->string('postal_cd')->change();
        });

    }
}
