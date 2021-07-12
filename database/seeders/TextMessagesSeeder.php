<?php

namespace Database\Seeders;

use App\Models\TextMessage;
use Illuminate\Database\Seeder;

class TextMessagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $text_messages = [
            [
                'text' => 'CANDAは日本屈指のベンチャー企業「HYBRID STYLE」の伝説の社内SE。',
            ],
            [
                'text' => 'そして、並外れた技術を持った時計技師でもあるんだ！',
            ],
            [
                'text' => 'このLINEボットではLaravelで作成したAPIのテストができるよ。',
            ],
            [
                'text' => 'いろいろ試して実装してみよう！',
            ],
            [
                'text' => 'DBに保存したパターンにマッチする文言をこのLINE上でメッセージとして送信してみよう！',
            ],
            [
                'text' => '意図した返答が返ってこない場合は、ロジックの誤りや保存データの不足がなど考えられるよ。',
            ],
            [
                'text' => '実装した後はバグがないか、いろいろなパターンでテストを行ってみてね♪',
            ],
        ];

        foreach($text_messages as $text_message){
            $model = new TextMessage();
            $model->text = $text_message['text'];
            $model->save();
        }
    }
}
