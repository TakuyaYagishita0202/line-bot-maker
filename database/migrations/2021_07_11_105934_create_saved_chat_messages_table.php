<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavedChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saved_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saved_chat_id')->constrained();
            $table->text('received_message')->comment('受信メッセージ');
            $table->text('sent_message')->comment('直前に送信したメッセージ');
            $table->timestamp('received_date')->comment('受信日時');
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
        Schema::dropIfExists('saved_chat_messages');
    }
}
