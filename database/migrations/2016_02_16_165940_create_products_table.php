<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_category_id')->unsigned();
            $table->string('name');
            $table->decimal('price1', 15, 2);
            $table->integer('qty1');
            $table->decimal('price2', 15, 2);
            $table->integer('qty2');
            $table->decimal('price3', 15, 2);
            $table->integer('qty3');
            $table->decimal('price4', 15, 2);
            $table->integer('qty4');
            $table->timestamps();

            $table->foreign('product_category_id')
                  ->references('id')
                  ->on('product_categories')
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
        Schema::drop('products');
    }
}
