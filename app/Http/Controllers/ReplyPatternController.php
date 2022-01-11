<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\ReplyPattern;
use App\Models\ReplyPatternMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReplyPatternController extends Controller
{
    public function index()
    {
        $reply_patterns = ReplyPattern::with(['reply_pattern_messages.message', 'reply_patterns.pattern_type'])
            ->where('user_id', Auth::user()->id)
            ->orderBy('rank')
            ->get();
        $pattern_types = array_column(DB::table('pattern_types')->get()->toArray(), 'name', 'id');

        return ['reply_patterns' => $reply_patterns, 'pattern_types' => $pattern_types];
    }

    public function store(Request $request) {
        $request = $request->all();

        $reply_pattern = ReplyPattern::where([
            ['id', $request['data']['id']],
            ['user_id', Auth::user()->id]
        ])->first();

        if(!$reply_pattern){
            // 新規作成
            $reply_pattern = new ReplyPattern();
            $isNew = true;
        } else {
            // 更新
            $isNew = false;
        }

        // 親のやりとりを反復するフラグ
        $repeat_parent = $request['parentId'] == $request['data']['state_reply_pattern_id'] ? true : false;
        
        // 子パターンが存在しない、かつ親のやりとりを反復しない場合は終端フラグを付ける
        if(empty($request['data']['children']) && !$repeat_parent){
            $last_flg = 1;
        } else {
            $last_flg = 0;
        }

        $reply_pattern->user_id = Auth::user()->id;
        $reply_pattern->name = $request['data']['title'];
        $reply_pattern->description = $request['data']['description'];
        $reply_pattern->pattern = $request['data']['pattern'];
        $reply_pattern->state_reply_pattern_id = $last_flg === 1 ? 0 : $request['data']['state_reply_pattern_id']; // 新規作成の場合はIDが決定していないため仮の値となる
        $reply_pattern->last_flg = $last_flg;
        $reply_pattern->parent_id = $request['parentId'];

        $parent_reply_pattern = ReplyPattern::find($request['parentId']);

        DB::beginTransaction();

        try {
            $reply_pattern->save();

            if($isNew && $last_flg === 0 && !$repeat_parent){
                // 新規作成時は保存後にIDが決定されるのでここでstate_reply_pattern_idを再度保存する
                $id = $reply_pattern->id;
                $reply_pattern->state_reply_pattern_id = $id;
                $reply_pattern->save();
            }

            if(!$isNew){
                // reply_patternに紐づく返信メッセージは一旦全て削除する
                $reply_pattern_messages = ReplyPatternMessage::where('reply_pattern_id', $reply_pattern->id)->get();
                if(!$reply_pattern_messages->isEmpty()){
                    foreach($reply_pattern_messages as $reply_pattern_message){
                        if($reply_pattern_message->message_id){
                            Message::find($reply_pattern_message->message_id)->delete();
                        }
                        $reply_pattern_message->delete();
                    }
                }
            } else {
                // 親パターンのlast_flgは0にする
                $parent_reply_pattern->last_flg = 0;
                $parent_reply_pattern->save();
            }

            // reply_pattern_message及びmessageの保存処理
            foreach($request['data']['reply_pattern_messages'] as $request_reply_pattern_message){
                $reply_pattern_message = new ReplyPatternMessage();

                if($request_reply_pattern_message['repeat_flg'] != 1){ // TODO: 新規作成時にrepeat_flgが存在しないので修正
                    // 新規作成
                    $message = new Message();
                    $message->message_type_id = $request_reply_pattern_message['message']['message_type_id'];
                    $message->text = $request_reply_pattern_message['message']['text'];
                    $message->save();
                    $message_id = $message->id;

                    $reply_pattern_message->message_id = $message_id;
                    $reply_pattern_message->repeat_flg = 0;
                } else {
                    $reply_pattern_message->repeat_flg = 1;
                }
                
                $reply_pattern_message->rank = $request_reply_pattern_message['rank'];
                $reply_pattern_message->reply_pattern_id = $reply_pattern->id;
                $reply_pattern_message->save();
            }

            // 子パターンのrankを更新
            foreach($request['data']['children'] as $request_child_reply_pattern){
                $child_reply_pattern = ReplyPattern::find($request_child_reply_pattern['id']);
                if(!$child_reply_pattern){
                    $child_reply_pattern->rank = $request_child_reply_pattern['rank'];
                    $child_reply_pattern->save();
                }
            }

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();

            dd($e->getMessage());
        }
    }
}
