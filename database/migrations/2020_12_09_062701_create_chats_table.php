<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('from');
            $table->unsignedBigInteger('to');
            $table->text('message');
            $table->boolean('friend_message')->default(1);
            $table->integer('reply_id')->nullable();
            $table->text('reply_message')->nullable();
            $table->dateTime('seen_at')->nullable();

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
        Schema::dropIfExists('chats');
    }
}
