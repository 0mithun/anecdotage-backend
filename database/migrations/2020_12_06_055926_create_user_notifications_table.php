<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->boolean('mention_notify_anecdotage')->default(0);
            $table->boolean('mention_notify_email')->default(0);
            $table->boolean('mention_notify_facebook')->default(0);

            $table->boolean('new_thread_posted_notify_anecdotage')->default(0);
            $table->boolean('new_thread_posted_notify_email')->default(0);
            $table->boolean('new_thread_posted_notify_facebook')->default(0);

            $table->boolean('receive_daily_random_thread_notify_anecdotage')->default(0);
            $table->boolean('receive_daily_random_thread_notify_email')->default(0);
            $table->boolean('receive_daily_random_thread_notify_facebook')->default(0);

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
        Schema::dropIfExists('user_notifications');
    }
}
