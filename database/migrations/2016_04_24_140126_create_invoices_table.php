<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('staff_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('invoice_no');
            $table->decimal('total', 15, 2);
            $table->decimal('paid', 15, 2);
            $table->decimal('change', 15, 2);
            $table->char('payment_status', 5);
            $table->date('payment_deadline');
            $table->decimal('staff_bonus', 15, 2);
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
        Schema::drop('invoices');
    }
}
