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
Route::post('callback/manager', 'Api\AfterAuditController@managerProcess');//品牌副总审批回调
//Route::post('callback/finance-officer', 'Api\AfterAuditController@financeOfficerProcess');//品牌副总审批回调
Route::post('callback/batch-callback','Api\AfterAuditController@batchApproveProcess');//品牌副总批量审批回调

