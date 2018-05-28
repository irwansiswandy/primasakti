<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCityStateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_state', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('state_id')->unsigned();
            $table->integer('city_id')->unsigned();
            $table->timestamps();

            $table->foreign('state_id')
                  ->references('id')
                  ->on('states')
                  ->onDelete('cascade');

            $table->foreign('city_id')
                  ->references('id')
                  ->on('cities')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('city_state');
    }
}
