<?php

namespace Database\Seeders;

use App\Models\ReplyPatternMessage;
use Illuminate\Database\Seeder;

class ReplyPatternMessagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reply_pattern_massages = [
            [
                'reply_pattern_id' => 1,
                'message_type_id' => 1,
                'message_id' => 1,
                'repeat_flg' => 0,
                'rank' => 1
            ],
            [
                'reply_pattern_id' => 1,
                'message_type_id' => 1,
                'message_id' => 2,
                'repeat_flg' => 0,
                'rank' => 2
            ],
            [
                'reply_pattern_id' => 2,
                'message_type_id' => 1,
                'message_id' => 3,
                'repeat_flg' => 0,
                'rank' => 1
            ],
            [
                'reply_pattern_id' => 2,
                'message_type_id' => 1,
                'message_id' => 4,
                'repeat_flg' => 0,
                'rank' => 2
            ],
            [
                'reply_pattern_id' => 3,
                'message_type_id' => 1,
                'message_id' => 5,
                'repeat_flg' => 0,
                'rank' => 1
            ],
            [
                'reply_pattern_id' => 3,
                'message_type_id' => 1,
                'message_id' => 6,
                'repeat_flg' => 0,
                'rank' => 2
            ],
        ];

        foreach($reply_pattern_massages as $reply_pattern_massage) {
            $model = new ReplyPatternMessage();
            $model->reply_pattern_id = $reply_pattern_massage['reply_pattern_id'];
            $model->message_type_id = $reply_pattern_massage['message_type_id'];
            $model->message_id = $reply_pattern_massage['message_id'];
            $model->repeat_flg = $reply_pattern_massage['repeat_flg'];
            $model->rank = $reply_pattern_massage['rank'];
            $model->save();
        }
    }
}
