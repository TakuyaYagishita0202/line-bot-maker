<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReplyPatternsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reply_patterns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('name', 255)->comment('応答パターン名');
            $table->text('pattern')->nullable()->comment('メッセージを検知に使用する正規表現');
            $table->integer('state_reply_pattern_id')->comment('reply_statesに格納するreply_pattern_id');
            $table->boolean('save_flg')->default(0)->comment('saved_chatに格納するか判定する為のフラグ');
            $table->boolean('last_flg')->default(0)->comment('末端の応答パターンか判定するフラグ');
            $table->bigInteger('parent_id');
            $table->unsignedInteger('rank')->nullable();
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
        Schema::dropIfExists('reply_patterns');
    }
}
