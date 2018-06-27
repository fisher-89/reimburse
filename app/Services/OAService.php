<?php

namespace App\Services;

use App\Services\CurlService;

class OAService
{
    protected $passport = true;

    public function __construct()
    {
        $this->oaPath = config('oa.oa_path');
        $this->clientId = config('oa.client_id');
        $this->clientSecret = config('oa.client_secret');
        $this->oaApiPath = config('oa.oaApiPath');
    }

    /**
     * 使用app_token与OA接口交互
     * @param string $url
     * @param type $params
     * @return type
     */
    public function getDataFromApi($url, $params = [])
    {
        $url = preg_match('/^https?:\/\//', $url) ? $url : $this->oaApiPath . '/' . trim($url, '/');
        if ($this->hasToken()) {
            $accessToken = session('oauth_access_token');
            $response = CurlService::build($url)
                ->setHeader(['Authorization:Bearer ' . $accessToken])
                ->sendMessageByPost($params);
            return $this->checkResponse($response);
        } elseif (!$this->passport) {
            if (!$this->hasClientToken()) {
                $this->getClientToken();
            }
            $clientToken = session('oauth_client_token');
            $response = CurlService::build($url)
                ->setHeader(['Authorization:Bearer ' . $clientToken])
                ->sendMessageByPost($params);
            return $this->checkResponse($response);
        } elseif (session()->has('oauth_refresh_token')) {
            $this->refreshAccessToken();
            return $this->getDataFromApi($url, $params);
        } elseif (request()->has('code')) {
            $this->getAccessToken();
            return $this->getDataFromApi($url, $params);
        } else {
            $this->getAuthCode();
        }
    }

    public function hasToken()
    {
        return session()->has('oauth_access_token') && session('oauth_expired_at') > time();
    }

    public function hasClientToken()
    {
        return session()->has('oauth_client_token') && session('oauth_client_expired_at') > time();
    }

    /**
     * 获取授权码
     * @return type
     */
    public function getAuthCode()
    {
        $url = $this->oaPath . '/oauth/authorize';
        $params = '?' . http_build_query([
                'client_id' => $this->clientId,
                'response_type' => 'code',
            ]);
        $url .= $params;
        header('Location:' . $url);
        die;
    }

    /**
     * 使用授权码获取访问令牌
     */
    public function getAccessToken()
    {
        $url = $this->oaPath . '/oauth/token';
        $authCode = request()->code;
        $message = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $authCode,
        ];
        $response = CurlService::build($url)->sendMessageByPost($message);
        if (isset($response['access_token'])) {
            $this->saveToken($response);
        } elseif (isset($response['message'])) {
            abort(500, $response['message']);
        } else {
            dd($response);
        }
    }

    /**
     * 刷新访问令牌
     * @return type
     */
    public function refreshAccessToken()
    {
        $url = $this->oaPath . '/oauth/token';
        $refreshToken = session('oauth_refresh_token');
        session()->forget('oauth_refresh_token');
        $message = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];
        $response = CurlService::build($url)->sendMessageByPost($message);
        if (isset($response['access_token'])) {
            $this->saveToken($response);
        } elseif (isset($response['message'])) {
            abort(500, $response['message']);
        } else {
            dd($response);
        }
    }

    public function getClientToken()
    {
        $url = $this->oaPath . '/oauth/token';
        $params = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];
        $response = CurlService::build($url)->sendMessageByPost($params);
        if (isset($response['access_token'])) {
            $this->saveClientToken($response);
        } elseif (isset($response['message'])) {
            abort(500, $response['message']);
        } else {
            dd($response);
        }
    }

    /**
     * 跳过passport验证，后台进程请求时使用
     * @return $this
     */
    public function withoutPassport()
    {
        $this->passport = false;
        return $this;
    }


    /**
     * 检查OA的返回值
     * @param $response
     * @return mixed
     */
    private function checkResponse($response)
    {
        if (isset($response['error']) && $response['error'] === 'Unauthenticated.') {
            session()->forget('oauth_access_token');
            session()->forget('oauth_refresh_token');
            session()->forget('oauth_expired_at');
            abort(401, '访问令牌无效');
        } elseif (isset($response['status'])) {
            return $response;
        } elseif (isset($response['message'])) {
            abort(500, $response['message']);
        }
    }

    protected function saveToken($response)
    {
        $accessToken = $response['access_token'];
        $refreshToken = $response['refresh_token'];
        $expiredAt = time() + $response['expires_in'];
        session()->put('oauth_access_token', $accessToken);
        session()->put('oauth_refresh_token', $refreshToken);
        session()->put('oauth_expired_at', $expiredAt);
    }

    protected function saveClientToken($response)
    {
        $clientToken = $response['access_token'];
        $expiresIn = time() + $response['expires_in'];
        session()->put('oauth_client_token', $clientToken);
        session()->put('oauth_client_expired_at', $expiresIn);
    }

}
