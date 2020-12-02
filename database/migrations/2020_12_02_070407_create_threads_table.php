<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug')->unique()->nullable();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('channel_id')->nullable();
            $table->string('title', 255);
            $table->text('body');

            $table->text('summary')->nullable();
            $table->string('source')->nullable();
            $table->string('main_subject')->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_path_pixel_color', 50)->nullable();
            $table->text('image_description')->nullable();
            $table->boolean('image_saved')->default(0);

            $table->string('cno')->nullable();
            $table->integer('age_restriction')->default(0);
            $table->boolean('anonymous')->default(0);

            $table->string('formatted_address')->nullable();
            $table->point('location')->nullable();

            $table->boolean('is_published')->default(1);
            //New Items
            $table->boolean('famous')->default(0);
            $table->text('slide_body')->nullable();
            $table->string('slide_image_pos')->nullable();
            $table->string('slide_color_bg')->nullable();
            $table->string('slide_color_0')->nullable();
            $table->string('slide_color_1')->nullable();
            $table->string('slide_color_2')->nullable();

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
        Schema::dropIfExists('threads');
    }
}
