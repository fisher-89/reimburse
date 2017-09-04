<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Services\CurlService;

class OAService {

    protected $appId; /* AppId */
    protected $appTicket; /* AppTicket */
    protected $oaApiPath; /* OA接口根路由 */
    protected $receiptUrl; /* 重定向地址 应指向getAppToken方法 */

    public function __construct() {
        $this->appId = config('oa.appId');
        $this->appTicket = config('oa.appTicket');
        $this->oaApiPath = config('oa.oaApiPath');
        $this->receiptUrl = config('oa.receiptUrl');
    }

    /**
     * 使用app_token与OA接口交互
     * @param string $url
     * @param type $params
     * @return type
     */
    public function getDataFromApi($url, $params = []) {
        if (session()->has('OA_staff_sn') && cache()->has('OA_appToken_' . session('OA_staff_sn'))) {
            $url = preg_match('/^https?:\/\//', $url) ? $url : $this->oaApiPath . $url;
            $params['app_token'] = cache('OA_appToken_' . session('OA_staff_sn'));
            $response = CurlService::build($url)->sendMessageByPost($params);
            if (!isset($response['status'])) {
                abort(500, '未知接口错误');
            } elseif ($response['status'] == -1 && $response['error_code'] == 503) {
                cache()->forget('OA_appToken_' . session('OA_staff_sn'));
                session()->forget('OA_refresh_token');
                session()->forget('OA_staff_sn');
            } else {
                return $response;
            }
        } elseif (session()->has('OA_refresh_token')) {
            $this->refreshAppToken();
            return $this->getDataFromApi($url, $params);
        } elseif (request()->has('auth_code')) {
            $this->getAppToken();
            return $this->getDataFromApi($url, $params);
        } else {
            if (empty($this->receiptUrl)) {
                $this->receiptUrl = url()->current();
            }
            $this->getAuthCode();
        }
    }

    /**
     * 获取授权码
     * @return type
     */
    public function getAuthCode() {
        $url = $this->oaApiPath . 'get_auth_code';
        $params = '?' . http_build_query(['app_id' => $this->appId, 'redirect_uri' => $this->receiptUrl]);
        $url .= $params;
        header('Location:' . $url);
        die;
    }

    /**
     * 使用授权码获取访问令牌
     * @param Request $request
     */
    public function getAppToken() {
        $url = $this->oaApiPath . 'get_token';
        $authCode = request()->auth_code;
        $redirectUri = trim(url()->current(), '/');
        $message = ['auth_code' => $authCode, 'redirect_uri' => $redirectUri, 'secret' => $this->makeSecret($authCode)];
        $response = CurlService::build($url)->sendMessageByPost($message);
        if (!isset($response['status'])) {
            echo $response;
            die;
        } elseif ($response['status'] == 1) {
            $this->saveAppToken($response['message']);
            return $this->makeAppTokenSuccessResponse($response);
        } elseif ($response['status'] == -1) {
            return $this->makeAppTokenErrorResponse($response);
        }
    }

    /**
     * 刷新访问令牌
     * @return type
     */
    public function refreshAppToken() {
        $url = $this->oaApiPath . 'refresh_token';
        $refreshToken = session('OA_refresh_token');
        $message = ['refresh_token' => $refreshToken];
        $response = CurlService::build($url)->sendMessageByPost($message);
        if (!isset($response['status'])) {
            echo $response;
            die;
        } elseif ($response['status'] == 1) {
            $this->saveAppToken($response['message']);
            return $this->makeAppTokenSuccessResponse($response);
        } elseif ($response['status'] == -1) {
            session()->forget('OA_refresh_token');
            return $this->makeAppTokenErrorResponse($response);
        }
    }

    /**
     * 生成app验证码
     * @param type $authCode
     * @return type
     */
    private function makeSecret($authCode) {
        return md5($this->appTicket . $authCode);
    }

    private function saveAppToken($response) {
        $appToken = $response['app_token'];
        $refreshToken = $response['refresh_token'];
        $staffSn = $response['staff_sn'];
        $expiration = $response['expiration'] - 1;
        cache()->put('OA_appToken_' . $staffSn, $appToken, $expiration);
        session()->put('OA_refresh_token', $refreshToken);
        session()->put('OA_staff_sn', $staffSn);
    }

    private function makeAppTokenSuccessResponse($response) {
        return 'app_token:' . $response['message']['app_token'];
    }

    private function makeAppTokenErrorResponse($response) {
        abort(500, $response['message']);
    }

}
