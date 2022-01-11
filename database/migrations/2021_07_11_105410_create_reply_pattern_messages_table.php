<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReplyPatternMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reply_pattern_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reply_pattern_id')->constrained()->cascadeOnDelete();
            $table->foreignId('message_type_id')->nullable()->constrained();
            $table->bigInteger('message_id')->nullable();
            $table->boolean('repeat_flg')->default(0)->comment('直前の受信メッセージを反復するかを判定するフラグ');
            $table->unsignedInteger('rank');
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
        Schema::dropIfExists('reply_pattern_messages');
    }
}
