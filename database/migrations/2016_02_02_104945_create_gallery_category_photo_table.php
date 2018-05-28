<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGalleryCategoryPhotoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gallery_category_photo', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gallery_category_id')->unsigned();
            $table->integer('gallery_photo_id')->unsigned();
            $table->timestamps();

            $table->foreign('gallery_category_id')
                  ->references('id')
                  ->on('gallery_categories')
                  ->onDelete('cascade');

            $table->foreign('gallery_photo_id')
                  ->references('id')
                  ->on('gallery_photos')
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
        Schema::drop('gallery_category_photo');
    }
}
