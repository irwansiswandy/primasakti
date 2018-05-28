<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            /* TABLES: USER ACCOUNT */
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->string('password', 60);
            $table->integer('user_level')->unsigned()->default(1);
            $table->boolean('wrote_review')->default(false);
            $table->boolean('verified')->default(false);
            $table->string('verification_token', 100)->nullable();
            $table->string('forgot_password_token', 100)->nullable();
            $table->rememberToken();

            /* TABLES: USER DATA */
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('postcode');
            $table->string('country');
            $table->string('phone');
            $table->string('cellphone');
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
        Schema::drop('users');
    }
}
