<?php

Route::group(['prefix' => 'communication.messages'], function () {
    Route::get('/', ['as' => 'communication.messages', 'uses' => 'CommunicationMessagesController@index']);
    Route::get('create', ['as' => 'communication.messages.create', 'uses' => 'CommunicationMessagesController@create']);
    Route::post('/', ['as' => 'communication.messages.store', 'uses' => 'CommunicationMessagesController@store']);
    Route::get('{id}', ['as' => 'communication.messages.show', 'uses' => 'CommunicationMessagesController@show']);
    Route::put('{id}', ['as' => 'communication.messages.update', 'uses' => 'CommunicationMessagesController@update']);
});
