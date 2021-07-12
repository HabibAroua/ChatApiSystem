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

Route::get('/messages', 'ApiController@fetchAllMessages');
Route::post('/sendMessage','ApiController@sendMessage');
Route::get('/allMobileUser','ApiController@allMobileUser');
Route::post('/sendMessageOneToOne', 'ApiController@sendMessageOneToOne');
