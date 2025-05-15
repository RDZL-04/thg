<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnTableUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('jk', 'gender');
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->dropColumn('two_factor_secret');
            $table->dropColumn('two_factor_recovery_codes');
            $table->dropColumn('email_verified');
            $table->dropColumn('email_verified_at');
            $table->dropColumn('id_sso');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('gender', 'jk');
            $table->dropColumn('date_of_birth');
            $table->text('two_factor_secret')
                    ->after('password')
                    ->nullable();
            $table->bigInteger('id_sso');
            $table->text('two_factor_recovery_codes')
                    ->after('two_factor_secret')
                    ->nullable();
            $table->boolean('email_verified')->after('phone')->nullable();
            $table->timestamp('email_verified_at')->nullable();
        });
    }
}
