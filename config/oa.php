<?php

/**
 * oa接口
 */
$http = env('OA_PATH', 'http://of.xigemall.com');

return [
    'oa_path' => $http,
    'client_id' => env('OA_CLIENT_ID'),
    'client_secret' => env('OA_CLIENT_SECRET'),
    'get_oa_dingtalk_js_api_ticket_url' => $http . '/api/get_dingtalk_js_api_ticket',
    'get_oa_dingtalk_access_token_url' => $http . '/api/get_dingtalk_access_token', //获取oa端钉钉的access_token地址
    'oaApiPath' => $http . '/api', /* OA接口根路由 */
    'login' => $http . '/api/get_current_user',//oa登录
    'logout' => $http . '/logout',//退出oa
    'get_user' => $http . '/api/get_user',//获取员工信息
];
