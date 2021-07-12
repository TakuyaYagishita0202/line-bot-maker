<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReplyStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reply_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('line_user_id', 255)->comment('送信元LINEユーザーのID');
            $table->bigInteger('reply_pattern_id')->comment('直前に行った応答のreply_pattern_idを格納（非アクティブの場合は0）');
            $table->timestamps();

            $table->unique(['user_id', 'line_user_id']); // LINEユーザーとボットでユニーク制約
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reply_states');
    }
}
