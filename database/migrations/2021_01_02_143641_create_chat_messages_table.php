<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('content');
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('sender_id');
            $table->string('username');
            $table->date('date');
            $table->timestamp('timestamp');
            $table->boolean('system')->default(true);
            $table->boolean('saved')->default(false);
            $table->boolean('distributed')->default(false);
            $table->boolean('seen')->default(false);
            $table->boolean('disable_actions')->default(false);
            $table->boolean('disable_reactions')->default(false);
            $table->timestamps();

            // _id: 7890,
            // content: 'message 1',
            // sender_id: 1234,
            // username: 'John Doe',
            // date: '13 November',
            // timestamp: '10:20',
            // system: false,
            // saved: true,
            // distributed: true,
            // seen: true,
            // disable_actions: false,
            // disable_reactions: false,
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
}
