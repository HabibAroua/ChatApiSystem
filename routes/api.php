<?php

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;

    Route::middleware('auth:api')->get
    (
        '/user', function (Request $request)
        {
            return $request->user();
        }
    );

    Route::post('/CreateUser','ApiMessageController@CreateUser');
    Route::get('/messages', 'ApiMessageController@fetchAllMessages');
    Route::post('/sendMessage','ApiMessageController@sendMessageOneToOne');
    Route::get('/allMobileUser','ApiMessageController@allMobileUser');
    Route::post('/sendMessageOneToOne', 'ApiMessageController@sendMessageOneToOne');
    Route::get('/getConversationByUser/{id}', 'ApiMessageController@getConversationByUser');
    Route::get('/allConversations/{id}','ApiMessageController@allConversations');
