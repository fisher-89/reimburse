<?php

/**
 * Curl类
 * create by Fisher <fisher9389@sina.com>
 */

namespace App\Services;

class CurlService {

    private $resource;
    private $url;
    private $port = 80;

    public static function build($url) {
        $curl = new CurlService();
        return $curl->setUrl($url);
    }

    public function get() {
        $this->init();
        curl_setopt($this->resource, CURLOPT_URL, $this->url);
        $response = curl_exec($this->resource);
        if ($response === false) {
            $response = curl_error($this->resource);
        }
        curl_close($this->resource);
        return $this->decodeResponse($response);
    }

    public function sendMessage($message) {
        $this->init();
        if (is_array($message)) {
            $data = '?' . http_build_query($message);
        } else {
            $data = $message;
        }
        $this->url .= $data;
        curl_setopt($this->resource, CURLOPT_HTTPGET, 1);
        $response = $this->get();
        return $response;
    }

    public function sendMessageByPost($message) {
        $this->init();
        curl_setopt($this->resource, CURLOPT_POST, true);
        $message = json_encode($message);
        curl_setopt($this->resource, CURLOPT_POSTFIELDS, $message);
        $this->setHeader(['Content-Type:application/json']);
        $response = $this->get();
        return $response;
    }

    public function setUrl($url) {
        $this->url = $url;
        $this->resource = curl_init();
        $preg = '/:\d+/';
        if (preg_match($preg, $url, $match)) {
            $this->port = (int) substr($match[0], 1);
            $url = str_replace($match[0], '', $url);
            curl_setopt($this->resource, CURLOPT_PORT, $this->port);
        }
        return $this;
    }

    private function init() {
        curl_setopt($this->resource, CURLOPT_HEADER, 0);
        curl_setopt($this->resource, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->resource, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($this->resource, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($this->resource, CURLOPT_REFERER, asset(url()->current()));
    }

    public function setHeader($headers) {
        curl_setopt($this->resource, CURLOPT_HTTPHEADER, $headers);
        return $this;
    }

    private function decodeResponse($response) {
        $responseArr = json_decode($response,true);
        if (json_last_error() == JSON_ERROR_NONE) {
            return $responseArr;
        }
        return $response;
    }

}
