<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/CreateUser','ApiMessageController@CreateUser');

Route::get('/messages', 'ApiMessageController@fetchAllMessages');
Route::post('/sendMessage','ApiMessageController@sendMessage');
Route::get('/allMobileUser','ApiMessageController@allMobileUser');
Route::post('/sendMessageOneToOne', 'ApiMessageController@sendMessageOneToOne');
Route::get('/getConversationByUser/{id}', 'ApiMessageController@getConversationByUser');
