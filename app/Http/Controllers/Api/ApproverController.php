<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ApproverController extends Controller {

    
    /**
     * 审批人信息存入缓存
     * @param Request $request
     */
    public function approverUserToCache() {
        app('Approver')->approverUserToCache();
    }

}
