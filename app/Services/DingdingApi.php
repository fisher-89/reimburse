<?php

/**
 * Curl类
 * create by Fisher 2016/9/5 <fisher9389@sina.com>
 */

namespace App\Services;

use App\Services\CurlService as Curl;
use Cache;

class DingdingApi
{
    public function getAccessToken()
    {
        $url = config('oa.get_oa_dingtalk_access_token_url');
        return app('OAService')->getDataFromApi($url)['message'];
//        return Curl::build($url)->get();//获取时不验证
    }

    /**
     * 发送text消息
     * @param $user_id
     * @param $content
     * @return mixed
     */
    public function sendTextMessages($user_id, $content)
    {
        $msgtype = 'text';
        $msgcontent = '{"content":' . $content . '}';
        $this->sendDingDingMessage($user_id, $msgcontent, $msgtype);
    }

    /**
     * 发送oa消息
     * @param $request
     * @param $reimburse
     * @param $user_id
     */
    public function sendOaMessage($msgContent, $user_id)
    {
        $msgtype = "oa";
        $this->sendDingDingMessage($user_id, $msgContent, $msgtype);
    }


    /* |---------------------------------| */
    /* |------------ private ------------| */
    /* |---------------------------------| */

    private function sendDingDingMessage($user_id, $msgcontent, $msgtype)
    {
//        $user_id = '0564652744672687';
        $url = config('dingding.message_url');
        $data = [
            'method' => config('dingding.method'),
            'session' => $this->getAccessToken(),
            'timestamp' => date('Y-m-d H:i:s'),
            'format' => 'json',
            'v' => '2.0',
            'simplify' => true,
            'msgtype' => $msgtype,
            'agent_id' => config('dingding.agentId'),
            'userid_list' => $user_id . ',lisi',
            'msgcontent' => $msgcontent,
        ];
        $result = Curl::build($url)->sendMessage($data);
        return $result;
    }
}
