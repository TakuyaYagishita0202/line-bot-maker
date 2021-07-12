<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureLineSignatureIsValid;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/test/{id}', function($id) {
    return response()->json(['message' => 'idは'.$id.'です。'], 200);
})->name('test');

Route::group(['middleware' => EnsureLineSignatureIsValid::class], function () {
    Route::post('/webhook/{id}', [WebhookController::class, 'callback'])->name('webhook');
});