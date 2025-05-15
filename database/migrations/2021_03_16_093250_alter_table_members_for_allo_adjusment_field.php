<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableMembersForAlloAdjusmentField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('members', function($table)
        {
            $table->dropColumn('email_verified');
            $table->dropColumn('password');
            $table->dropColumn('id_role');
            $table->dropColumn('id_sso');
            $table->dropColumn('email_verified_at');
            $table->string('date_of_birth',8)->nullable()->after('phone');
            $table->string('id_type',10)->nullable()->after('date_of_birth');
            $table->string('id_no',25)->nullable()->after('id_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('members', function($table)
        {
            $table->boolean('email_verified')->nullable()->after('phone');
            $table->string('password')->after('email_verified');
            $table->bigInteger('id_role')->nullable()->after('password');
            $table->bigInteger('id_sso')->nullable()->after('id_role');
            $table->timestamp('email_verified_at')->nullable()->after('gender');
            $table->dropColumn('date_of_birth');
            $table->dropColumn('id_type');
            $table->dropColumn('id_no');
        });
    }
}
