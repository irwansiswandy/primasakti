<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('day');
            $table->integer('open_hour');
            $table->integer('open_minute');
            $table->integer('closed_hour');
            $table->integer('closed_minute');
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
        Schema::drop('shop_schedules');
    }
}
