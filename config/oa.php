<?php

/**
 * oa接口
 */
$http = 'http://of.xigemall.com/';
//$http = 'http://a15678598u.iask.in/';

return [
    'get_oa_dingtalk_js_api_ticket_url'=>$http.'api/get_dingtalk_js_api_ticket',
    'get_oa_dingtalk_access_token_url' => $http . 'api/get_dingtalk_access_token', //获取oa端钉钉的access_token地址
    'appId' => 1, /* AppId */
    'appTicket' => 'NuzfBSvgglKsc8DgFfXTHgaFMFBDyOCr', /* AppTicket */
    'oaApiPath' => $http . 'api/', /* OA接口根路由 */
    'receiptUrl' => null, /* 重定向地址 应指向getAppToken方法 */
    'login' => $http . 'api/get_current_user',//oa登录
    'logout' => $http . 'logout',//退出oa
    'get_user'=>$http.'api/get_user',//获取员工信息
];
