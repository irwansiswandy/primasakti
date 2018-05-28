<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no');
            $table->integer('invoice_id')->unsigned();
            $table->integer('staff_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->text('note');
            $table->decimal('down_payment', 15, 2);
            $table->dateTime('deadline');
            $table->dateTime('finished_at');
            $table->integer('status')->default(0); /* 0 = PENDING, 1 = PROCESSED, 2 = FINISHED */
            $table->boolean('taken')->default(false);
            $table->dateTime('taken_at');
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
        Schema::drop('orders');
    }
}
