<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavedChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saved_chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->text('line_user_id')->comment('送信元LINEユーザーのID');
            $table->foreignId('reply_pattern_id')->constrained()->comment('始点reply_pattern_id');
            $table->boolean('completed_flg')->default(0)->comment('一連のチャットが完了したことを判定する為のフラグ');
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
        Schema::dropIfExists('saved_chats');
    }
}
