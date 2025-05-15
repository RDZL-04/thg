<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatedMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('fullname',100);
            $table->string('email',100)->unique();
            $table->string('phone',15)->unique();
            $table->boolean('email_verified')->nullable();
            $table->string('password');
            $table->bigInteger('id_role')->nullable();
            $table->bigInteger('id_sso')->nullable();
            $table->boolean('gender')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('image',150)->nullable();
            $table->string('city',5)->nullable();
            $table->string('country',5)->nullable();
            $table->string('state_province',100)->nullable();
            $table->integer('postal_cd')->nullable();
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
        Schema::dropIfExists('members');
    }
}
