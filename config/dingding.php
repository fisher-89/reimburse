<?php

return [
    /**
     * 服务端接口地址
     */
//    'server_api' => 'https://oapi.dingtalk.com/',
    /**
     * 企业ID
     */
//    'CorpId' => 'dingb8f2e19299cab872',
    /**
     * 企业密钥
     */
//    'CorpSecret' => 'uM21n9Zp4YXZm9MUCJeHo7YVyz3SelJ02X5PmnDFN7fZ8gef8AAQ2NSodDAvNar4',
    /**
     * 微应用ID
     */
//    'agentId' => 39806381,
    'agentId' => 49053312,//测试应用
    /**
     * 签名随机字符串
     */
//    'nonceStr' => 'EGh32fgTue345',
    /**
     * 企业会话消息异步发送
     */
    'message_url' => 'https://eco.taobao.com/router/rest',
    'method'=>'dingtalk.corp.message.corpconversation.asyncsend',

];
