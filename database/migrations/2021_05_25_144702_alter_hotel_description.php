<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterHotelDescription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotels', function ($table) {
			$table->text('description')->change();
			$table->string('mice_email',100)->after('email_notification')->nullable();
			$table->string('mice_wa',15)->after('mice_email')->nullable();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotels', function ($table) {
			$table->string('description')->change();
			$table->dropColumn('mice_email');
			$table->dropColumn('mice_wa');
		});
    }
}
