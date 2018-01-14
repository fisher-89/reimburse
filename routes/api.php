<?php

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
Route::group(['namespace' => 'Api'], function () {
    Route::group(['prefix' => 'reimburse'], function () {//报销系统
        Route::get('/approverCache', ['uses' => 'ApproverController@approverUserToCache']);//审批人存入缓存
    });
});

