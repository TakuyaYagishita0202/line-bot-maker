<?php

namespace App\Http\Controllers;

use App\Models\MessageType;
use App\Models\ReplyPattern;
use App\Models\ReplyPatternMessage;
use App\Models\ReplyState;
use App\Models\TextMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function callback(Request $request, $id)
    {
        $user = User::find($id);
        if(!$user) {
            // TODO: webhookの登録URLに誤りがある旨を返信する
            abort(400);
        }

        $channel_access_token = $user->channel_access_token;
        $channel_secret = $user->channel_secret;

        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($channel_access_token);
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $channel_secret]);

        $signature = $_SERVER['HTTP_'.\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
        $events = $bot->parseEventRequest($request->getContent(), $signature);

        foreach ($events as $event) {
            switch (true){
                case $event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage: // TODO: 現状はテキストメッセージのみ検知する
                    $reply_token = $event->getReplyToken();

                    // 以下、後で削除
                    // $reply_message = '受信しました！';
                    // $bot->replyText($reply_token, $reply_message);
                    // break;

                    $line_user_id = $event->getUserId();
                    if(!$line_user_id){
                        // LINEユーザーID取得失敗の為エラー
                        $reply_message = 'エラーが発生しました。';
                        $bot->replyText($reply_token, $reply_message);
                        break;
                    }

                    $reply_state = ReplyState::where([
                        ['line_user_id', $line_user_id],
                        ['user_id', $id]
                    ])->first();

                    if(!$reply_state || $reply_state->reply_pattern_id == 0){
                        // 特定のLINEユーザーから初めてのチャットがあった場合
                        $reply_patterns = ReplyPattern::where('parent_id', 0)->orderBy('rank')->get();
                    } else {
                        // 特定のLINEユーザーと既にチャットが進行していた場合
                        $reply_patterns = $reply_state->reply_pattern->reply_patterns;
                    }

                    if($reply_patterns->isEmpty()){
                        // 子パターンが未設定の為エラー
                        $reply_message = 'エラーが発生しました。';
                        if($reply_state){
                            // reply_stateを初期化
                            $reply_state->reply_pattern_id = 0;
                            $reply_state->save();
                        }
                        $bot->replyText($reply_token, $reply_message);
                        break;
                    }

                    // 条件にマッチするreply_patternを探索
                    $matched_pattern = null;
                    foreach($reply_patterns as $reply_pattern) {
                        if(preg_match($reply_pattern->pattern, $event->getText()) > 0){
                            $matched_pattern = $reply_pattern;
                            break;
                        }
                    }
                    if(!$matched_pattern){
                        // 全ての条件にマッチしなかった為エラー
                        $reply_message = '入力内容に誤りがあります。お手数をおかけしまして申し訳ございませんが、再度最初からやり直してください。';
                        if($reply_state){
                            // reply_stateを初期化
                            $reply_state->reply_pattern_id = 0;
                            $reply_state->save();
                        }
                        $bot->replyText($reply_token, $reply_message);
                        break;
                    } else {
                        // パターンに属するメッセージを全て送信して、reply_stateも更新する
                        if($matched_pattern->state_reply_pattern_id != $matched_pattern->id && $matched_pattern->state_reply_pattern_id != 0){
                            $reply_patern_messages = ReplyPatternMessage::where('reply_pattern_id', $matched_pattern->state_reply_pattern_id)->orderBy('rank')->get();
                        } else {
                            $reply_patern_messages = $matched_pattern->reply_pattern_messages;
                        }
                        $multiMessageBuilder = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
                        foreach($reply_patern_messages as $reply_pattern_message){
                            $repeat_flg = $reply_pattern_message->repeat_flg;
                            if($repeat_flg == 1){
                                $message_type = MessageType::find(1);    
                            } else {
                                $message_type = MessageType::find($reply_pattern_message->message_type_id);
                            }
                            
                            if($message_type->prefix == 'text'){
                                // TODO: 現状emojiに対応していない
                                if($repeat_flg == 1){
                                    $reply_message = $event->getText();
                                } else {
                                    $reply_message = (TextMessage::where('id', $reply_pattern_message->message_id)->first())->text;
                                }
                                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($reply_message);
                                $multiMessageBuilder->add($textMessageBuilder);
                            }
                        }
                        $bot->replyMessage($reply_token, $multiMessageBuilder);

                        if(!$reply_state){
                            $reply_state = new ReplyState();
                            $reply_state->user_id = $id;
                            $reply_state->line_user_id = $line_user_id;
                        }

                        if($matched_pattern->last_flg == 1){
                            // 終端フラグが立っている場合は状態を初期化して一連のやりとりを終了する
                            $reply_state->reply_pattern_id = 0;
                        } else {
                            $reply_state->reply_pattern_id = $matched_pattern->state_reply_pattern_id;
                        }
                        $reply_state->save();
                    }
                    break;
            }
        }
    }
}
