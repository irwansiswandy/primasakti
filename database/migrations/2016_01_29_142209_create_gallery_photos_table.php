<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGalleryPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gallery_photos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned();
            $table->string('folder_name');
            $table->string('file_name');
            $table->string('thumb_name');
            $table->string('file_path');
            $table->string('thumb_path');
            $table->timestamps();

            $table->foreign('category_id')
                  ->references('id')
                  ->on('gallery_categories')
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
        Schema::drop('gallery_photos');
    }
}
