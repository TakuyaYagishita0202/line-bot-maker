<?php

namespace Database\Seeders;

use App\Models\ReplyPattern;
use Illuminate\Database\Seeder;

class ReplyPatternsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reply_patterns = [
            [
                'user_id' => 1,
                'name' => 'CANDAって誰？',
                'pattern' => '/CANDAって誰？/',
                'state_reply_pattern_id' => 0, // 仮の値
                'save_flg' => 0,
                'last_flg' => 1,
                'parent_id' => 0,
                'rank' => 1
            ],
            [
                'user_id' => 1,
                'name' => 'なにができるの？',
                'pattern' => '/なにができるの？/',
                'state_reply_pattern_id' => 0, // 仮の値
                'save_flg' => 0,
                'last_flg' => 1,
                'parent_id' => 0,
                'rank' => 2
            ],
            [
                'user_id' => 1,
                'name' => '取り扱い説明書',
                'pattern' => '/取り扱い説明書/',
                'state_reply_pattern_id' => 0, // 仮の値
                'save_flg' => 0,
                'last_flg' => 1,
                'parent_id' => 0,
                'rank' => 3
            ],
        ];

        foreach($reply_patterns as $reply_pattern) {
            $model = new ReplyPattern();
            $model->user_id = $reply_pattern['user_id'];
            $model->name = $reply_pattern['name'];
            $model->pattern = $reply_pattern['pattern'];
            $model->state_reply_pattern_id = $reply_pattern['state_reply_pattern_id'];
            $model->save_flg = $reply_pattern['save_flg'];
            $model->last_flg = $reply_pattern['last_flg'];
            $model->parent_id = $reply_pattern['parent_id'];
            $model->rank = $reply_pattern['rank'];
            $model->save();

            $model->state_reply_pattern_id = $model->id;
            $model->save();
        }
    }
}
