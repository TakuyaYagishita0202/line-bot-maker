<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnsureLineSignatureIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $signature = $_SERVER['HTTP_'.\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
        $user = User::find($request->id);

        if(!$user){
            // ユーザーが存在しない場合
            abort(400);
        }

        $channel_secret = $user->channel_secret;
        if (!\LINE\LINEBot\SignatureValidator::validateSignature(
                $request->getContent(),
                $channel_secret,
                $signature
        )) {
            abort(400);
        }

        return $next($request);
    }
}
